<?php

namespace Tests\Unit;

use App\Models\AttendanceSession;
use App\Services\AttendanceEvaluationService;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AttendanceEvaluationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('session_date');
            $table->string('session_status')->default('closed');
            $table->dateTime('clock_in_time')->nullable();
            $table->dateTime('clock_out_time')->nullable();
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_late')->default(false);
            $table->string('shift_window')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('attendance_sessions');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    /** @test */
    public function it_evaluates_absent_day_when_user_has_no_sessions()
    {
        $user = User::create(['name' => 'Alice Tech', 'email' => 'alice@fts.ae']);
        $service = new AttendanceEvaluationService();

        $result = $service->evaluateDay($user->id, Carbon::parse('2026-09-15'));

        $this->assertTrue($result['success']);
        $this->assertEquals('absent', $result['status']);
        $this->assertEquals(0, $result['sessions_count']);
        $this->assertEquals(0, $result['total_hours']);
        $this->assertEquals(0, $result['total_minutes']);
        $this->assertFalse($result['has_late']);
        $this->assertEmpty($result['late_sessions']);
        $this->assertStringContainsString('Absent', $result['message']);
    }

    /** @test */
    public function it_evaluates_present_day_with_sessions_and_late_flags()
    {
        $user = User::create(['name' => 'Bob Engineer', 'email' => 'bob@fts.ae']);
        $date = Carbon::parse('2026-09-16');

        AttendanceSession::create([
            'user_id' => $user->id,
            'session_date' => $date->toDateString(),
            'session_status' => 'closed',
            'clock_in_time' => Carbon::parse('2026-09-16 08:45:00'),
            'duration_minutes' => 240,
            'is_late' => true,
            'shift_window' => 'Shift 1',
        ]);

        AttendanceSession::create([
            'user_id' => $user->id,
            'session_date' => $date->toDateString(),
            'session_status' => 'closed',
            'clock_in_time' => Carbon::parse('2026-09-16 13:00:00'),
            'duration_minutes' => 240,
            'is_late' => false,
            'shift_window' => 'Shift 2',
        ]);

        $service = new AttendanceEvaluationService();
        $result = $service->evaluateDay($user->id, $date);

        $this->assertTrue($result['success']);
        $this->assertEquals('present', $result['status']);
        $this->assertEquals(2, $result['sessions_count']);
        $this->assertEquals(480, $result['total_minutes']);
        $this->assertEquals(8.0, $result['total_hours']);
        $this->assertTrue($result['has_late']);
        $this->assertCount(1, $result['late_sessions']);
        $this->assertEquals('Shift 1', $result['late_sessions'][0]['shift']);
        $this->assertStringContainsString('Present - 2 session(s)', $result['message']);
    }

    /** @test */
    public function it_uses_current_day_when_date_is_null_in_evaluate_day()
    {
        $user = User::create(['name' => 'Charlie Field', 'email' => 'charlie@fts.ae']);
        $service = new AttendanceEvaluationService();

        $result = $service->evaluateDay($user->id, null);

        $this->assertTrue($result['success']);
        $this->assertEquals(Carbon::today()->format('Y-m-d'), $result['date']);
    }

    /** @test */
    public function it_handles_exception_and_returns_failure_in_evaluate_day()
    {
        $service = new AttendanceEvaluationService();
        $result = $service->evaluateDay(999999); // Non-existent user

        $this->assertFalse($result['success']);
        $this->assertNull($result['status']);
        $this->assertStringContainsString('Error evaluating attendance', $result['message']);
    }

    /** @test */
    public function it_evaluates_day_for_all_users()
    {
        $user1 = User::create(['name' => 'Tech 1', 'email' => 't1@fts.ae']);
        $user2 = User::create(['name' => 'Tech 2', 'email' => 't2@fts.ae']);
        $date = Carbon::parse('2026-09-17');

        AttendanceSession::create([
            'user_id' => $user1->id,
            'session_date' => $date->toDateString(),
            'session_status' => 'closed',
            'clock_in_time' => Carbon::parse('2026-09-17 08:00:00'),
            'duration_minutes' => 300,
            'is_late' => false,
            'shift_window' => 'Shift 1',
        ]);

        AttendanceSession::create([
            'user_id' => $user2->id,
            'session_date' => $date->toDateString(),
            'session_status' => 'open', // not closed, so evaluateDay will mark absent
            'clock_in_time' => Carbon::parse('2026-09-17 08:00:00'),
            'duration_minutes' => 0,
            'is_late' => false,
        ]);

        $service = new AttendanceEvaluationService();
        $batchResult = $service->evaluateDayForAllUsers($date);

        $this->assertTrue($batchResult['success']);
        $this->assertEquals(2, $batchResult['evaluated_count']);
        $this->assertEquals(1, $batchResult['present_count']);
        $this->assertEquals(1, $batchResult['absent_count']);
        $this->assertCount(2, $batchResult['results']);
        $this->assertStringContainsString('Evaluated 1 present and 1 absent', $batchResult['message']);
    }

    /** @test */
    public function it_handles_batch_evaluation_exception()
    {
        Schema::dropIfExists('attendance_sessions'); // Causes DB error

        $service = new AttendanceEvaluationService();
        $result = $service->evaluateDayForAllUsers(Carbon::parse('2026-09-17'));

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error in batch evaluation', $result['message']);
    }

    /** @test */
    public function it_generates_user_attendance_summary_for_date_range()
    {
        $user = User::create(['name' => 'Supervisor Sam', 'email' => 'sam@fts.ae']);
        $start = Carbon::parse('2026-09-01');
        $end = Carbon::parse('2026-09-03');

        // Day 1: normal session
        AttendanceSession::create([
            'user_id' => $user->id,
            'session_date' => '2026-09-01',
            'session_status' => 'closed',
            'clock_in_time' => Carbon::parse('2026-09-01 08:00:00'),
            'duration_minutes' => 480,
            'is_late' => false,
        ]);

        // Day 2: late session
        AttendanceSession::create([
            'user_id' => $user->id,
            'session_date' => '2026-09-02',
            'session_status' => 'closed',
            'clock_in_time' => Carbon::parse('2026-09-02 09:15:00'),
            'duration_minutes' => 420,
            'is_late' => true,
        ]);

        $service = new AttendanceEvaluationService();
        $summary = $service->getUserAttendanceSummary($user->id, $start, $end);

        $this->assertTrue($summary['success'], $summary['message'] ?? 'Failed');
        $this->assertEquals(2, $summary['total_days']);
        $this->assertEquals(2, $summary['present_days']);
        $this->assertEquals(1, $summary['late_days']);
        $this->assertEquals(900, $summary['total_minutes']);
        $this->assertEquals(15.0, $summary['total_hours']);
        $this->assertCount(2, $summary['daily_breakdown']);
    }

    /** @test */
    public function it_handles_summary_exception_for_invalid_user()
    {
        $service = new AttendanceEvaluationService();
        $result = $service->getUserAttendanceSummary(888888, Carbon::now(), Carbon::now());

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error fetching attendance summary', $result['message']);
    }
}
