<?php

namespace App\Modernization\Module1\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Proof of Concept: Asynchronous PDF Document Generation Job
 * 
 * Offloads CPU-intensive DOMPDF document rendering (financial vouchers, invoices,
 * purchase order PDFs) to a background queue, preventing PHP-FPM web thread blocking.
 */
class PocPdfReportJob implements ShouldQueue
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
        $docType = $this->payload['document_type'] ?? 'PurchaseOrder';
        $entityId = $this->payload['entity_id'] ?? 101;
        $simulatedPath = 'storage/app/reports/' . strtolower($docType) . "_{$entityId}_" . time() . '.pdf';

        Log::info('[POC Module 1] Asynchronous PDF Generation Job Started', [
            'job_id' => $this->job ? $this->job->getJobId() : 'sync_runner',
            'document_type' => $docType,
            'entity_id' => $entityId,
            'target_path' => $simulatedPath,
        ]);

        // Simulating background rendering
        usleep(2500);

        $executionTimeMs = round((microtime(true) - $start) * 1000, 2);

        Log::info('[POC Module 1] PDF Generated and Stored in Background Storage', [
            'status' => 'COMPLETED',
            'document_type' => $docType,
            'entity_id' => $entityId,
            'output_file' => $simulatedPath,
            'execution_time_ms' => $executionTimeMs,
        ]);

        return [
            'status' => 'SUCCESS',
            'service' => 'DOMPDF Asynchronous Worker',
            'output_file' => $simulatedPath,
            'execution_time_ms' => $executionTimeMs,
        ];
    }
}
