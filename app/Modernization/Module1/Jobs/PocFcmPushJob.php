<?php

namespace App\Modernization\Module1\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Proof of Concept: Asynchronous FCM Mobile Push Notification Job
 * 
 * Offloads mobile push notifications from the HTTP lifecycle to background queues.
 */
class PocFcmPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    public $payload;

    /**
     * Create a new job instance.
     *
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        $start = microtime(true);
        $title = $this->payload['title'] ?? 'FTS Portal Alert';
        $tokensCount = count($this->payload['tokens'] ?? ['mock_token_1']);
        $channel = $this->payload['channel'] ?? 'attendance_alerts';

        Log::info('[POC Module 1] Asynchronous FCM Push Job Processing Started', [
            'job_id' => $this->job ? $this->job->getJobId() : 'sync_runner',
            'title' => $title,
            'recipients_count' => $tokensCount,
            'channel' => $channel,
        ]);

        // Non-blocking processing simulation
        usleep(1200);

        $executionTimeMs = round((microtime(true) - $start) * 1000, 2);

        Log::info('[POC Module 1] FCM Push Notification Broadcast Succeeded', [
            'status' => 'BROADCAST_COMPLETED',
            'tokens_reached' => $tokensCount,
            'execution_time_ms' => $executionTimeMs,
        ]);

        return [
            'status' => 'SUCCESS',
            'service' => 'FCM Push Asynchronous Worker',
            'tokens_reached' => $tokensCount,
            'execution_time_ms' => $executionTimeMs,
        ];
    }
}
