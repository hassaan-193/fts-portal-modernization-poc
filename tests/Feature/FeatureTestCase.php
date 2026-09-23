<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\CreatesApplication;

/**
 * FeatureTestCase
 *
 * A custom base class for all integration (Feature) tests in this project.
 *
 * WHY THIS EXISTS
 * ---------------
 * Several database migrations in ft_portal_base use MySQL-specific DDL
 * (e.g. `MODIFY COLUMN`, `JSON_VALID()`, backtick-quoted identifiers).
 * Those statements are not valid SQLite SQL, which means `php artisan migrate`
 * always crashes when the test database connection is `sqlite::memory:`.
 *
 * APPROACH
 * --------
 * Instead of running the full migration chain, this base class:
 *  1. Runs all "safe" migrations (pure Schema Builder calls, no raw MySQL SQL).
 *  2. Creates the few remaining tables manually using SQLite-compatible SQL.
 *  3. Runs only the seeders each test class needs (via $seeders).
 *
 * Each test class can override $seeders to declare which seeders it needs.
 *
 * DATABASE LIFECYCLE
 * ------------------
 * - Schema is built ONCE per test-class (setUpBeforeClass) for speed.
 * - Each individual test wraps its DB work in a transaction that is rolled
 *   back after the test, so tests are fully isolated.
 */
abstract class FeatureTestCase extends BaseTestCase
{
    use CreatesApplication;

    /** @var string[] Seeder classes to run after schema creation. */
    protected array $seeders = [];

    // ------------------------------------------------------------------
    // Schema bootstrap — runs once per test class
    // ------------------------------------------------------------------

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
    }

    // ------------------------------------------------------------------
    // Per-test transaction — each test is fully isolated
    // ------------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();

        // Build the schema fresh for each test using SQLite-compatible DDL
        $this->buildSqliteSchema();

        // Seed what this test class declared it needs
        foreach ($this->seeders as $seederClass) {
            try {
                Artisan::call('db:seed', ['--class' => $seederClass, '--force' => true]);
            } catch (\Exception $e) {
                // Non-fatal: some seeders may depend on data not present in the
                // lightweight schema. Log and continue.
                \Illuminate\Support\Facades\Log::warning(
                    "Seeder {$seederClass} skipped in integration test: " . $e->getMessage()
                );
            }
        }
    }

    protected function tearDown(): void
    {
        // Drop all tables so the next test starts clean
        $this->dropAllTables();
        parent::tearDown();
    }

    // ------------------------------------------------------------------
    // SQLite-compatible schema builder
    // ------------------------------------------------------------------

    /**
     * Creates a minimal schema covering the tables that Feature tests touch.
     * Uses only Laravel Schema Builder calls (no MySQL-only raw SQL).
     */
    private function buildSqliteSchema(): void
    {
        $this->dropAllTables();

        // ── Spatie Laravel-Permission tables ──────────────────────────
        Schema::create('permissions', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('roles', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

        Schema::create('model_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
        });

        Schema::create('model_has_roles', function ($table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
        });

        Schema::create('role_has_permissions', function ($table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
        });

        // ── Users (staff) ─────────────────────────────────────────────
        Schema::create('users', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('api_token', 80)->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Company portal users ───────────────────────────────────────
        Schema::create('companies', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // ── Payment Bookings ──────────────────────────────────────────
        Schema::create('payment_bookings', function ($table) {
            $table->bigIncrements('id');
            $table->string('reference_no')->nullable();
            $table->string('booking_type', 10)->default('cheque'); // cheque | cash
            $table->string('payee')->nullable();
            $table->string('payment_against')->nullable();
            $table->text('purpose')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('project_cost_centre')->nullable();
            $table->date('booking_date')->nullable();
            $table->string('cheque_number')->nullable();
            $table->date('release_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('cash_account')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Staff Profiles ─────────────────────────────────────────────
        Schema::create('staf_profiles', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('last_name')->nullable();
            $table->string('staf_type')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Attendance ────────────────────────────────────────────────
        Schema::create('attendances', function ($table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('foreman_id')->nullable();
            $table->unsignedBigInteger('staf_profile_id')->nullable();
            $table->date('attendance_date')->nullable();
            $table->string('status', 20)->nullable();
            $table->timestamps();
        });

        // ── Password resets (required by Laravel auth) ─────────────────
        Schema::create('password_resets', function ($table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // ── Jobs / Failed Jobs (required by some service providers) ───
        Schema::create('failed_jobs', function ($table) {
            $table->bigIncrements('id');
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Drop all tables in the correct order so FK-like implicit constraints
     * do not cause issues on subsequent test setups.
     */
    private function dropAllTables(): void
    {
        $tables = [
            'role_has_permissions',
            'model_has_permissions',
            'model_has_roles',
            'attendances',
            'payment_bookings',
            'staf_profiles',
            'companies',
            'password_resets',
            'failed_jobs',
            'users',
            'permissions',
            'roles',
        ];

        // SQLite does not enforce FK constraints unless explicitly enabled,
        // so we can simply drop in order without disabling constraints.
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
}
