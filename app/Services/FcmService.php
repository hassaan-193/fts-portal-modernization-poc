<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Sends push notifications through FCM HTTP v1.
 *
 * Deliberately has no SDK dependency: the OAuth2 assertion is a JWT signed with
 * openssl, and the send is a plain curl POST. The alternative was pulling
 * google/auth through composer, which this environment could not download
 * reliably.
 *
 * Note the app's older App\Services\PushNotificationService targets
 * https://fcm.googleapis.com/fcm/send — the legacy API Google retired in 2024.
 * It is dead code; this class replaces it for the payment-booking module.
 *
 * Configuration lives in config/services.php under `fcm`.
 */
class FcmService
{
    /** Access tokens last an hour; re-minting per send would be wasteful. */
    const TOKEN_CACHE_KEY = 'fcm_access_token';
    const TOKEN_TTL_SECONDS = 3300; // 55 minutes, leaving headroom

    /**
     * Whether push is configured at all. Callers should treat a false here as
     * "skip quietly" — a missing key must never break a booking decision.
     */
    public function isConfigured()
    {
        $path = $this->credentialsPath();

        return $path !== null && is_readable($path);
    }

    private function credentialsPath()
    {
        $path = config('services.fcm.credentials');

        if (empty($path)) {
            return null;
        }

        // Allow a path relative to storage/app for convenience.
        return file_exists($path) ? $path : storage_path('app/' . ltrim($path, '/'));
    }

    private function credentials()
    {
        $path = $this->credentialsPath();

        if (!$path || !is_readable($path)) {
            return null;
        }

        $json = json_decode(file_get_contents($path), true);

        return is_array($json) && isset($json['client_email'], $json['private_key'])
            ? $json
            : null;
    }

    /**
     * An OAuth2 access token for the messaging scope, cached until it nears
     * expiry.
     *
     * @return string|null
     */
    public function accessToken($forceFresh = false)
    {
        if (!$forceFresh) {
            $cached = Cache::get(self::TOKEN_CACHE_KEY);
            if ($cached) {
                return $cached;
            }
        }

        $sa = $this->credentials();
        if (!$sa) {
            return null;
        }

        $now = time();
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claim = [
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => $sa['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600,
        ];

        $unsigned = $this->b64(json_encode($header)) . '.' . $this->b64(json_encode($claim));

        $signature = '';
        if (!openssl_sign($unsigned, $signature, $sa['private_key'], 'sha256WithRSAEncryption')) {
            Log::error('FcmService: could not sign the JWT assertion');

            return null;
        }

        $assertion = $unsigned . '.' . $this->b64($signature);

        $response = $this->post($sa['token_uri'], [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $assertion,
        ], false);

        $token = $response['body']['access_token'] ?? null;

        if (!$token) {
            Log::error('FcmService: token request failed', [
                'status' => $response['status'],
                'body'   => $response['raw'],
            ]);

            return null;
        }

        Cache::put(self::TOKEN_CACHE_KEY, $token, now()->addSeconds(self::TOKEN_TTL_SECONDS));

        return $token;
    }

    /**
     * Send one notification to every device belonging to the given users.
     *
     * FCM HTTP v1 has no multicast endpoint, so this loops. Volumes here are a
     * handful of reviewers, not a broadcast list.
     *
     * @param  array $userIds
     * @param  array $data    string=>string only; FCM rejects other types
     * @return array{sent:int,failed:int,pruned:int}
     */
    public function sendToUsers(array $userIds, $title, $body, array $data = [])
    {
        $tokens = DeviceToken::forUsers($userIds);

        if (empty($tokens)) {
            return ['sent' => 0, 'failed' => 0, 'pruned' => 0];
        }

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToTokens(array $tokens, $title, $body, array $data = [])
    {
        $result = ['sent' => 0, 'failed' => 0, 'pruned' => 0];

        if (!$this->isConfigured()) {
            Log::info('FcmService: not configured, skipping push');

            return $result;
        }

        $accessToken = $this->accessToken();
        if (!$accessToken) {
            return $result;
        }

        $sa = $this->credentials();
        $url = 'https://fcm.googleapis.com/v1/projects/' . $sa['project_id'] . '/messages:send';

        // FCM requires every data value to be a string.
        $data = array_map(function ($v) { return (string) $v; }, $data);

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token'        => $token,
                    'notification' => ['title' => $title, 'body' => $body],
                    'data'         => $data,
                    'android'      => [
                        'priority'     => 'high',
                        'notification' => [
                            // Lets the app group and route the tap.
                            'channel_id'   => 'payment_bookings',
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        ],
                    ],
                ],
            ];

            $response = $this->post($url, $payload, true, $accessToken);

            if ($response['status'] === 200) {
                $result['sent']++;
                continue;
            }

            $result['failed']++;

            // 404 UNREGISTERED / 400 INVALID_ARGUMENT mean the token is dead —
            // drop it so the table does not fill with stale installs.
            $status = $response['body']['error']['details'][0]['errorCode']
                ?? ($response['body']['error']['status'] ?? '');

            if (in_array($response['status'], [400, 404], true)
                && in_array($status, ['UNREGISTERED', 'INVALID_ARGUMENT', 'NOT_FOUND'], true)) {
                DeviceToken::where('token', $token)->delete();
                $result['pruned']++;
                continue;
            }

            Log::warning('FcmService: send failed', [
                'status' => $response['status'],
                'body'   => $response['raw'],
            ]);
        }

        return $result;
    }

    // ---------------------------------------------------------------- helpers

    private function b64($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * @return array{status:int,body:array|null,raw:string}
     */
    private function post($url, array $payload, $asJson = true, $bearer = null)
    {
        $ch = curl_init($url);

        $headers = [];
        if ($asJson) {
            $headers[] = 'Content-Type: application/json';
            $body = json_encode($payload);
        } else {
            $body = http_build_query($payload);
        }
        if ($bearer) {
            $headers[] = 'Authorization: Bearer ' . $bearer;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_POSTFIELDS     => $body,
        ]);

        $raw    = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err    = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            Log::warning('FcmService: curl failed', ['error' => $err, 'url' => $url]);

            return ['status' => 0, 'body' => null, 'raw' => $err];
        }

        return ['status' => $status, 'body' => json_decode($raw, true), 'raw' => (string) $raw];
    }
}
