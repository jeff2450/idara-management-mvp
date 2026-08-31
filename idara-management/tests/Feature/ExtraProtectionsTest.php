<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\DepartmentTransaction;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Angalia architecture.md §5 / prd.md §7: "ulinzi wa ziada kwa data ya Idara
 * ya Watoto na miamala/fedha". Hizi zinathibitisha kwamba ulinzi huo wa ziada
 * upo KWELI, siyo tu maelezo kwenye architecture.md.
 */
class ExtraProtectionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_regular_member_cannot_view_department_transactions(): void
    {
        $department = Department::factory()->create();

        $member = User::factory()->create();
        $member->assignRole('member');
        $department->users()->attach($member->id, ['role' => 'member']);

        DepartmentTransaction::factory()->for($department)->create([
            'recorded_by' => User::factory()->create()->id,
        ]);

        $this->actingAs($member)
            ->get(route('departments.transactions.index', $department))
            ->assertForbidden();
    }

    public function test_leader_can_view_and_record_department_transactions(): void
    {
        $department = Department::factory()->create();

        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');
        $department->users()->attach($leader->id, ['role' => 'leader']);

        $this->actingAs($leader)
            ->get(route('departments.transactions.index', $department))
            ->assertOk();

        $this->actingAs($leader)->post(route('departments.transactions.store', $department), [
            'type' => 'Michango',
            'amount' => 50000,
            'occurred_at' => now()->format('Y-m-d'),
        ])->assertRedirect(route('departments.transactions.index', $department));

        $this->assertDatabaseHas('department_transactions', [
            'department_id' => $department->id,
            'type' => 'Michango',
        ]);
    }

    public function test_admin_and_leader_can_manage_membership_of_a_sensitive_department(): void
    {
        $watoto = Department::factory()->create(['is_sensitive' => true]);

        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');
        $watoto->users()->attach($leader->id, ['role' => 'leader']);

        // Kiongozi wa Idara ana admin privileges sasa
        $this->actingAs($leader)->post(route('departments.members.store', $watoto), [
            'mode' => 'new',
            'role' => 'member',
            'name' => 'Mtoto Fulani',
            'email' => 'mzazi1@example.test',
            'password' => 'password123',
        ])->assertRedirect(route('departments.show', $watoto));

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Admin pia anaweza.
        $this->actingAs($admin)->post(route('departments.members.store', $watoto), [
            'mode' => 'new',
            'role' => 'member',
            'name' => 'Mtoto Fulani 2',
            'email' => 'mzazi2@example.test',
            'password' => 'password123',
        ])->assertRedirect(route('departments.show', $watoto));
    }

    public function test_leader_can_manage_membership_of_a_normal_non_sensitive_department(): void
    {
        $vijana = Department::factory()->create(['is_sensitive' => false]);

        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');
        $vijana->users()->attach($leader->id, ['role' => 'leader']);

        $this->actingAs($leader)->post(route('departments.members.store', $vijana), [
            'mode' => 'new',
            'role' => 'member',
            'name' => 'Kijana Fulani',
            'email' => 'kijana@example.test',
            'password' => 'password123',
        ])->assertRedirect(route('departments.show', $vijana));
    }
}
