<?php

namespace Tests\Feature;

use App\Models\AnnualSchedule;
use App\Models\Department;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Muundo huu unafuata tests/Feature/DepartmentScopingTest.php - kanuni ile
 * ile ("taarifa za idara moja hazivuki kwenda idara nyingine", prd.md §6.2)
 * lazima ithibitike kwa Awamu ya 3 (ratiba ya mwaka) pia.
 */
class Phase3ScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_leader_only_sees_their_own_department_schedule(): void
    {
        $vijana = Department::factory()->create();
        $wamama = Department::factory()->create();

        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');
        $vijana->users()->attach($leader->id, ['role' => 'leader']);

        AnnualSchedule::factory()->for($vijana)->create(['title' => 'Kambi ya Vijana']);
        AnnualSchedule::factory()->for($wamama)->create(['title' => 'Semina ya Wamama']);

        $response = $this->actingAs($leader)->get(route('schedules.index', $vijana));

        $response->assertOk();
        $response->assertViewHas('schedules', fn ($schedules) => $schedules->total() === 1);
    }

    public function test_leader_cannot_open_another_departments_schedule_page(): void
    {
        $vijana = Department::factory()->create();
        $wamama = Department::factory()->create();

        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');
        $vijana->users()->attach($leader->id, ['role' => 'leader']);

        // Kama ilivyo kwenye DepartmentScopingTest: DepartmentVisibilityScope
        // inaifanya $wamama isionekane kabisa kwa kiongozi huyu, hivyo route
        // model binding inashindwa kabla hata middleware ya department.access
        // haijafika - matokeo ni 404, siyo 403.
        $this->actingAs($leader)
            ->get(route('schedules.index', $wamama))
            ->assertNotFound();
    }

    public function test_member_cannot_create_schedule_items(): void
    {
        $vijana = Department::factory()->create();

        $member = User::factory()->create();
        $member->assignRole('member');
        $vijana->users()->attach($member->id, ['role' => 'member']);

        $this->actingAs($member)->post(route('schedules.store', $vijana), [
            'title' => 'Jaribio',
            'planned_year' => now()->year,
            'planned_month' => 5,
        ])->assertForbidden();
    }
}
