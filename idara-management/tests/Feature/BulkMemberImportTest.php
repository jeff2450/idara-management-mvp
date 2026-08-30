<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BulkMemberImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_leader_can_download_member_import_template(): void
    {
        $dept = Department::factory()->create(['name' => 'Idara ya Vijana', 'slug' => 'idara-ya-vijana']);
        $leader = User::factory()->create();
        $leader->syncRoles(['idara_leader']);
        $dept->users()->attach($leader->id, ['role' => 'leader']);

        $response = $this->actingAs($leader)
            ->get(route('departments.members.template', $dept));

        $response->assertOk();
        $response->assertHeader('Content-Disposition', 'attachment; filename="template-wanachama-idara-ya-vijana.csv"');
    }

    public function test_leader_can_bulk_import_members_via_csv(): void
    {
        $dept = Department::factory()->create(['name' => 'Idara ya Vijana', 'slug' => 'idara-ya-vijana']);
        $leader = User::factory()->create();
        $leader->syncRoles(['idara_leader']);
        $dept->users()->attach($leader->id, ['role' => 'leader']);

        $csvContent = "Jina Kamili,Namba ya Simu,Barua Pepe\n"
            . "Elisha Baraka,0711223344,elisha@example.com\n"
            . "Hellen Joseph,0755667788,\n"
            . "Baraka James,0799887766,baraka@example.com\n";

        $file = UploadedFile::fake()->createWithContent('wanachama.csv', $csvContent);

        $response = $this->actingAs($leader)
            ->post(route('departments.members.import', $dept), [
                'file' => $file,
                'role' => 'member',
            ]);

        $response->assertRedirect(route('departments.show', $dept));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('users', ['name' => 'Elisha Baraka', 'phone' => '0711223344']);
        $this->assertDatabaseHas('users', ['name' => 'Hellen Joseph', 'phone' => '0755667788']);
        $this->assertDatabaseHas('users', ['name' => 'Baraka James', 'phone' => '0799887766']);

        $elisha = User::where('name', 'Elisha Baraka')->first();
        $this->assertTrue($dept->members()->where('users.id', $elisha->id)->exists());
    }

    public function test_existing_member_is_attached_without_error(): void
    {
        $dept = Department::factory()->create();
        $leader = User::factory()->create();
        $leader->syncRoles(['idara_leader']);
        $dept->users()->attach($leader->id, ['role' => 'leader']);

        $existingUser = User::factory()->create(['name' => 'John Existing', 'email' => 'john.existing@example.com']);

        $csvContent = "Jina,Simu,Email\n"
            . "John Existing,0700000000,john.existing@example.com\n";

        $file = UploadedFile::fake()->createWithContent('members.csv', $csvContent);

        $response = $this->actingAs($leader)
            ->post(route('departments.members.import', $dept), [
                'file' => $file,
                'role' => 'member',
            ]);

        $response->assertRedirect(route('departments.show', $dept));
        $this->assertTrue($dept->members()->where('users.id', $existingUser->id)->exists());
    }

    public function test_regular_member_cannot_import(): void
    {
        $dept = Department::factory()->create();
        $member = User::factory()->create();
        $member->syncRoles(['member']);
        $dept->users()->attach($member->id, ['role' => 'member']);

        $file = UploadedFile::fake()->createWithContent('members.csv', "Jina,Simu\nTest,07000\n");

        $response = $this->actingAs($member)
            ->post(route('departments.members.import', $dept), [
                'file' => $file,
                'role' => 'member',
            ]);

        $response->assertForbidden();
    }
}
