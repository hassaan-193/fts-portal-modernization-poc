<?php

namespace App\Modernization\Module1\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Proof of Concept: Asynchronous WhatsApp Notification Job
 * 
 * Replaces synchronous 60s timeout / sleep() calls inside HTTP requests
 * with a non-blocking background queue job.
 */
class PocWhatsAppJob implements ShouldQueue
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
        $recipient = $this->payload['recipient'] ?? 'N/A';
        $messageType = $this->payload['type'] ?? 'General Notification';
        $referenceId = $this->payload['reference_id'] ?? null;
        $simulatedPayloadBytes = strlen(json_encode($this->payload));

        Log::info('[POC Module 1] Asynchronous WhatsApp Job Processing Started', [
            'job_id' => $this->job ? $this->job->getJobId() : 'sync_runner',
            'type' => $messageType,
            'recipient' => $recipient,
            'reference_id' => $referenceId,
            'payload_bytes' => $simulatedPayloadBytes,
        ]);

        // Simulate asynchronous dispatch processing (micro-delay < 2ms, zero worker blocking)
        usleep(1500);

        $executionTimeMs = round((microtime(true) - $start) * 1000, 2);

        Log::info('[POC Module 1] WhatsApp Job Dispatched Successfully via Background Worker', [
            'status' => 'DELIVERED',
            'recipient' => $recipient,
            'execution_time_ms' => $executionTimeMs,
            'worker_pool_impact' => '0% web worker starvation (decoupled to queue)'
        ]);

        return [
            'status' => 'SUCCESS',
            'service' => 'WhatsApp Asynchronous Worker',
            'recipient' => $recipient,
            'execution_time_ms' => $executionTimeMs,
        ];
    }
}
