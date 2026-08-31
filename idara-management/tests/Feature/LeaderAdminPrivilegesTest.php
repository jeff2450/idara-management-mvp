<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\LetterTemplate;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderAdminPrivilegesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_leader_can_create_a_department(): void
    {
        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');

        $response = $this->actingAs($leader)->post(route('departments.store'), [
            'name' => 'Idara ya Wazee',
            'description' => 'Huduma kwa wazee',
            'is_sensitive' => false,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('departments', [
            'name' => 'Idara ya Wazee',
        ]);
    }

    public function test_leader_can_update_a_department(): void
    {
        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');

        $dept = Department::factory()->create(['name' => 'Idara ya Mazoezi']);

        $response = $this->actingAs($leader)->put(route('departments.update', $dept), [
            'name' => 'Idara ya Mazoezi na Afya',
            'description' => 'Imebadilishwa',
            'is_sensitive' => false,
        ]);

        $response->assertRedirect(route('departments.show', $dept));
        $this->assertDatabaseHas('departments', [
            'id' => $dept->id,
            'name' => 'Idara ya Mazoezi na Afya',
        ]);
    }

    public function test_leader_can_manage_letter_templates(): void
    {
        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');

        // Create
        $response = $this->actingAs($leader)->post(route('letter-templates.store'), [
            'name' => 'Mwaliko Maalum',
            'body_template' => 'Habari {{name}}, tunakualika.',
        ]);

        $response->assertRedirect(route('letter-templates.index'));
        $this->assertDatabaseHas('letter_templates', [
            'name' => 'Mwaliko Maalum',
        ]);

        $template = LetterTemplate::first();

        // Update
        $this->actingAs($leader)->put(route('letter-templates.update', $template), [
            'name' => 'Mwaliko Mpya',
            'body_template' => 'Habari {{name}}, karibu sana.',
        ])->assertRedirect(route('letter-templates.index'));

        // Delete
        $this->actingAs($leader)->delete(route('letter-templates.destroy', $template))
            ->assertRedirect(route('letter-templates.index'));

        $this->assertDatabaseMissing('letter_templates', [
            'id' => $template->id,
        ]);
    }

    public function test_navigation_redirects_do_not_return_404(): void
    {
        $dept = Department::factory()->create(['name' => 'Idara ya Vijana']);
        $leader = User::factory()->create();
        $leader->assignRole('idara_leader');
        $dept->users()->attach($leader->id, ['role' => 'leader']);

        $this->actingAs($leader)->get('/dashboard')->assertRedirect(route('dashboard'));
        $this->actingAs($leader)->get('/departments')->assertRedirect(route('departments.index'));
        $this->actingAs($leader)->get('/wanachama')->assertRedirect(route('departments.show', $dept));
        $this->actingAs($leader)->get('/sms')->assertRedirect(route('departments.sms.index', $dept));
        $this->actingAs($leader)->get('/barua')->assertRedirect(route('departments.letters.index', $dept));
        $this->actingAs($leader)->get('/ripoti')->assertRedirect(route('departments.reports.index', $dept));
        $this->actingAs($leader)->get('/miamala')->assertRedirect(route('departments.transactions.index', $dept));
        $this->actingAs($leader)->get('/ratiba')->assertRedirect(route('schedules.index', $dept));
        $this->actingAs($leader)->get('/maendeleo')->assertRedirect(route('departments.progress', $dept));
    }
}
