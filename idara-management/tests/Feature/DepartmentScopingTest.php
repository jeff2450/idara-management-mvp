<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Hizi ndizo tabia muhimu zaidi za usalama zilizoainishwa kwenye prd.md §6.2/6.3
 * na architecture.md §5: "Taarifa za idara moja hazivuki kwenda idara nyingine".
 */
class DepartmentScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
    }

    public function test_guest_cannot_view_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_admin_sees_all_departments(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        Department::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('departments.index'));

        $response->assertOk();
        $response->assertViewHas('departments', fn ($departments) => $departments->total() === 3);
    }

    public function test_member_only_sees_their_own_department(): void
    {
        $watoto = Department::factory()->create(['name' => 'Idara ya Watoto']);
        $vijana = Department::factory()->create(['name' => 'Idara ya Vijana']);

        $member = User::factory()->create();
        $member->assignRole('member');
        $watoto->users()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($member)->get(route('departments.index'));

        $response->assertOk();
        $response->assertViewHas('departments', function ($departments) use ($watoto) {
            return $departments->total() === 1 && $departments->first()->is($watoto);
        });
    }

    public function test_member_cannot_open_a_department_they_do_not_belong_to(): void
    {
        $watoto = Department::factory()->create();
        $vijana = Department::factory()->create();

        $member = User::factory()->create();
        $member->assignRole('member');
        $watoto->users()->attach($member->id, ['role' => 'member']);

        // Global Scope inazuia hata implicit route-model-binding kuiona idara
        // asiyomo - hivyo matokeo ni 404, siyo 403 (haifichui hata kuwepo kwake).
        $this->actingAs($member)
            ->get(route('departments.show', $vijana))
            ->assertNotFound();
    }

    public function test_leader_can_add_a_member_to_department(): void
    {
        $vijana = Department::factory()->create();

        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');
        $vijana->users()->attach($leader->id, ['role' => 'leader']);

        $this->actingAs($leader)->post(route('departments.members.store', $vijana), [
            'mode' => 'new',
            'role' => 'member',
            'name' => 'Mwanachama Mpya',
            'email' => 'mwanachama@example.test',
            'password' => 'password123',
        ])->assertRedirect(route('departments.show', $vijana));

        $this->assertDatabaseHas('department_user', [
            'department_id' => $vijana->id,
            'role' => 'member',
        ]);
    }

    public function test_leader_has_admin_privilege_to_assign_another_leader(): void
    {
        $vijana = Department::factory()->create();

        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');
        $vijana->users()->attach($leader->id, ['role' => 'leader']);

        $response = $this->actingAs($leader)->post(route('departments.members.store', $vijana), [
            'mode' => 'new',
            'role' => 'leader',
            'name' => 'Kiongozi Mwingine',
            'email' => 'kiongozi2@example.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('departments.show', $vijana));
        $this->assertDatabaseHas('department_user', [
            'department_id' => $vijana->id,
            'role' => 'leader',
        ]);
    }
}
