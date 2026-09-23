<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Modernization\Module2\Services\PocPhp83Modernizer;
use App\Modernization\Module2\Services\PocFrameworkUpgradeAnalyzer;
use App\Modernization\Module2\Services\PocPackageCompatibilityValidator;

class PocModule2Test extends TestCase
{
    /**
     * Test Step 1: PHP 8.3 modernizer runs computation benchmark and returns valid syntax maps.
     *
     * @return void
     */
    public function test_php83_modernizer_executes_and_returns_valid_metrics()
    {
        $modernizer = new PocPhp83Modernizer();
        $results = $modernizer->runModernizationAudit();

        $this->assertArrayHasKey('current_runtime', $results);
        $this->assertArrayHasKey('target_runtime', $results);
        $this->assertStringContainsString('8.3', $results['target_runtime']);
        $this->assertGreaterThan(0, $results['benchmark']['execution_time_ms']);
        $this->assertArrayHasKey('constructor_property_promotion', $results['syntax_modernizations']);
        $this->assertArrayHasKey('match_expressions', $results['syntax_modernizations']);
    }

    /**
     * Test Step 2: Framework upgrade analyzer validates 4 sequential stepping stones.
     *
     * @return void
     */
    public function test_framework_upgrade_analyzer_validates_stepping_stones()
    {
        $analyzer = new PocFrameworkUpgradeAnalyzer();
        $results = $analyzer->analyzeUpgradePath();

        $this->assertCount(4, $results['stepping_stones'], 'Must define all 4 upgrade stages (7->8, 8->9, 9->10, 10->11)');
        $this->assertEquals('Laravel 11.x LTS (Target State)', $results['stepping_stones']['stage_4']['to']);
        $this->assertArrayHasKey('spatie/laravel-permission', $results['package_audits']);
        $this->assertArrayHasKey('fideloper/proxy', $results['package_audits']);
    }

    /**
     * Test Step 3: Package compatibility validator verifies Spatie RBAC and DOMPDF upgrade readiness.
     *
     * @return void
     */
    public function test_package_compatibility_validator_verifies_spatie_and_dompdf()
    {
        $validator = new PocPackageCompatibilityValidator();
        $results = $validator->validatePackageCompatibility();

        $this->assertArrayHasKey('spatie_permission', $results);
        $this->assertArrayHasKey('spatie_medialibrary', $results);
        $this->assertArrayHasKey('dompdf_reporting', $results);
        $this->assertStringContainsString('v6.x', $results['spatie_permission']['target_version']);
    }

    /**
     * Test Step 4: The full Module 2 Artisan command executes and exits with success code 0.
     *
     * @return void
     */
    public function test_module2_artisan_command_executes_successfully()
    {
        $this->artisan('modernize:poc-module2')->assertExitCode(0);
    }
}
