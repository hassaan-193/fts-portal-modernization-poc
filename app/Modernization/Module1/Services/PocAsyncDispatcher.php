<?php

namespace App\Modernization\Module1\Services;

use App\Modernization\Module1\Jobs\PocWhatsAppJob;
use App\Modernization\Module1\Jobs\PocFcmPushJob;
use App\Modernization\Module1\Jobs\PocPdfReportJob;
use Illuminate\Support\Facades\Log;

/**
 * Proof of Concept: Modernized Asynchronous Dispatcher Service
 * 
 * Demonstrates the non-blocking service pattern that controllers will use to offload
 * heavy operations to background workers.
 */
class PocAsyncDispatcher
{
    /**
     * Dispatch WhatsApp message to background queue.
     *
     * @param string $recipient
     * @param string $message
     * @param array $meta
     * @return array
     */
    public function dispatchWhatsApp(string $recipient, string $message, array $meta = []): array
    {
        $start = microtime(true);

        $payload = array_merge([
            'recipient' => $recipient,
            'message' => $message,
            'type' => 'WhatsApp Notification',
            'dispatched_at' => now()->toIso8601String(),
        ], $meta);

        // Dispatch to queue
        PocWhatsAppJob::dispatch($payload);

        $dispatchLatencyMs = round((microtime(true) - $start) * 1000, 3);

        Log::info('[POC Module 1] Dispatched WhatsApp Job to Queue', [
            'recipient' => $recipient,
            'dispatch_overhead_ms' => $dispatchLatencyMs,
            'status' => 'QUEUED_NON_BLOCKING',
        ]);

        return [
            'dispatched' => true,
            'job' => 'PocWhatsAppJob',
            'queue' => 'default',
            'overhead_ms' => $dispatchLatencyMs,
        ];
    }

    /**
     * Dispatch FCM push notification to background queue.
     *
     * @param array $tokens
     * @param string $title
     * @param string $body
     * @return array
     */
    public function dispatchFcmPush(array $tokens, string $title, string $body): array
    {
        $start = microtime(true);

        $payload = [
            'tokens' => $tokens,
            'title' => $title,
            'body' => $body,
            'channel' => 'mobile_push',
            'dispatched_at' => now()->toIso8601String(),
        ];

        PocFcmPushJob::dispatch($payload);

        $dispatchLatencyMs = round((microtime(true) - $start) * 1000, 3);

        Log::info('[POC Module 1] Dispatched FCM Push Job to Queue', [
            'tokens_count' => count($tokens),
            'dispatch_overhead_ms' => $dispatchLatencyMs,
            'status' => 'QUEUED_NON_BLOCKING',
        ]);

        return [
            'dispatched' => true,
            'job' => 'PocFcmPushJob',
            'queue' => 'default',
            'overhead_ms' => $dispatchLatencyMs,
        ];
    }

    /**
     * Dispatch PDF report generation to background queue.
     *
     * @param string $documentType
     * @param int|string $entityId
     * @return array
     */
    public function dispatchPdfGeneration(string $documentType, $entityId): array
    {
        $start = microtime(true);

        $payload = [
            'document_type' => $documentType,
            'entity_id' => $entityId,
            'dispatched_at' => now()->toIso8601String(),
        ];

        PocPdfReportJob::dispatch($payload);

        $dispatchLatencyMs = round((microtime(true) - $start) * 1000, 3);

        Log::info('[POC Module 1] Dispatched PDF Report Job to Queue', [
            'document_type' => $documentType,
            'entity_id' => $entityId,
            'dispatch_overhead_ms' => $dispatchLatencyMs,
            'status' => 'QUEUED_NON_BLOCKING',
        ]);

        return [
            'dispatched' => true,
            'job' => 'PocPdfReportJob',
            'queue' => 'default',
            'overhead_ms' => $dispatchLatencyMs,
        ];
    }
}
