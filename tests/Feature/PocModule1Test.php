<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Queue;
use App\Modernization\Module1\Jobs\PocWhatsAppJob;
use App\Modernization\Module1\Jobs\PocFcmPushJob;
use App\Modernization\Module1\Jobs\PocPdfReportJob;
use App\Modernization\Module1\Services\PocAsyncDispatcher;
use App\Modernization\Module1\Services\PocQueryOptimizer;
use App\Modernization\Module1\Services\PocPermissionCacheService;

class PocModule1Test extends TestCase
{
    /**
     * Test Step 1: Background queued job dispatching.
     *
     * @return void
     */
    public function test_async_background_jobs_dispatch_without_blocking()
    {
        Queue::fake();

        $dispatcher = new PocAsyncDispatcher();

        // 1. WhatsApp dispatch
        $dispatcher->dispatchWhatsApp('+971500000000', 'Test PO Message', ['reference_id' => 999]);
        Queue::assertPushed(PocWhatsAppJob::class, function ($job) {
            return $job->payload['recipient'] === '+971500000000';
        });

        // 2. FCM Push dispatch
        $dispatcher->dispatchFcmPush(['tok_123'], 'Test Alert', 'Test Push Body');
        Queue::assertPushed(PocFcmPushJob::class, function ($job) {
            return in_array('tok_123', $job->payload['tokens']);
        });

        // 3. PDF Generation dispatch
        $dispatcher->dispatchPdfGeneration('Invoice', 501);
        Queue::assertPushed(PocPdfReportJob::class, function ($job) {
            return $job->payload['document_type'] === 'Invoice' && $job->payload['entity_id'] === 501;
        });
    }

    /**
     * Test Step 2: Asynchronous worker job execution and non-blocking handling.
     *
     * @return void
     */
    public function test_async_jobs_execute_and_produce_success_status()
    {
        $waJob = new PocWhatsAppJob([
            'recipient' => '+971501234567',
            'type' => 'Unit Test Alert',
        ]);
        $waResult = $waJob->handle();
        $this->assertEquals('SUCCESS', $waResult['status']);

        $fcmJob = new PocFcmPushJob([
            'title' => 'Unit Test Push',
            'tokens' => ['tok_1', 'tok_2'],
        ]);
        $fcmResult = $fcmJob->handle();
        $this->assertEquals('SUCCESS', $fcmResult['status']);

        $pdfJob = new PocPdfReportJob([
            'document_type' => 'Invoice',
            'entity_id' => 123,
        ]);
        $pdfResult = $pdfJob->handle();
        $this->assertEquals('SUCCESS', $pdfResult['status']);
    }

    /**
     * Test Step 3: N+1 query optimization significantly reduces query count.
     *
     * @return void
     */
    public function test_eager_loading_reduces_query_count()
    {
        $optimizer = new PocQueryOptimizer();
        $results = $optimizer->runBenchmark(10);

        $baseline = $results['baseline']['total_queries'];
        $optimized = $results['optimized']['total_queries'];

        $this->assertGreaterThan(0, $baseline);
        $this->assertLessThanOrEqual($baseline, $optimized);
        $this->assertEquals(0, $results['optimized']['duplicate_queries'], 'Eager loading must eliminate all duplicate queries');
    }

    /**
     * Test Step 4: Role and permission caching eliminates repeated database queries.
     *
     * @return void
     */
    public function test_permission_caching_eliminates_database_queries_on_warm_request()
    {
        $permService = new PocPermissionCacheService();
        $results = $permService->runBenchmark();

        $this->assertEquals(0, $results['warm_cached']['sql_queries'], 'Warm permission check must require 0 SQL queries');
    }

    /**
     * Test Step 5: The full Artisan command executes and exits with success code 0.
     *
     * @return void
     */
    public function test_artisan_command_executes_successfully()
    {
        $this->artisan('modernize:poc-module1')->assertExitCode(0);
    }
}
