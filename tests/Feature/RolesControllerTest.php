<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run seeders for dependencies
        $this->seed(\Database\Seeders\LeaveStatusesTableSeeder::class);
        $this->seed(\Database\Seeders\LeaveTypesTableSeeder::class);
        $this->seed(\Database\Seeders\RolesTableSeeder::class);
    }

    /** @test */
    public function super_admin_can_view_roles_page()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->get('/admin/roles');

        $response->assertStatus(200);
        $response->assertViewIs('admin.roles.index');
    }

    /** @test */
    public function super_admin_can_get_roles_json()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)
            ->getJson('/admin/roles');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        $this->assertGreaterThan(0, count($response->json('data')));
    }

    /** @test */
    public function super_admin_can_create_role()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)
            ->postJson('/admin/roles', [
                'role_name' => 'Test Role',
                'role_description' => 'Test Description',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Role created successfully!',
        ]);

        $this->assertDatabaseHas('roles', [
            'role_name' => 'Test Role',
            'role_description' => 'Test Description',
        ]);
    }

    /** @test */
    public function cannot_create_duplicate_role()
    {
        $admin = $this->createSuperAdmin();

        // Create first role
        $this->actingAs($admin)
            ->postJson('/admin/roles', [
                'role_name' => 'Unique Role',
                'role_description' => 'Description',
            ]);

        // Try to create duplicate
        $response = $this->actingAs($admin)
            ->postJson('/admin/roles', [
                'role_name' => 'Unique Role',
                'role_description' => 'Different Description',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['role_name']);
    }

    /** @test */
    public function super_admin_can_update_role()
    {
        $admin = $this->createSuperAdmin();
        $role = Role::create([
            'role_name' => 'Old Name',
            'role_description' => 'Old Description',
            'role_status' => 1,
        ]);

        $response = $this->actingAs($admin)
            ->putJson("/admin/roles/{$role->id}", [
                'role_name' => 'Updated Name',
                'role_description' => 'Updated Description',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'role_name' => 'Updated Name',
            'role_description' => 'Updated Description',
        ]);
    }

    /** @test */
    public function cannot_edit_super_admin_role()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)
            ->putJson('/admin/roles/1', [
                'role_name' => 'Hacked Admin',
                'role_description' => 'Should not work',
            ]);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Cannot edit the Super Admin role.',
        ]);
    }

    /** @test */
    public function cannot_delete_super_admin_role()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)
            ->deleteJson('/admin/roles/1');

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Cannot delete the Super Admin role.',
        ]);
    }

    /** @test */
    public function super_admin_can_delete_unused_role()
    {
        $admin = $this->createSuperAdmin();
        $role = Role::create([
            'role_name' => 'Deletable Role',
            'role_description' => 'Can be deleted',
            'role_status' => 1,
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/admin/roles/{$role->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    /** @test */
    public function non_admin_cannot_access_roles()
    {
        // Explicitly use ID 999 to avoid ID 1 which gets super admin access
        $user = User::factory()->create(['id' => 999]);

        $response = $this->actingAs($user)->get('/admin/roles');

        $response->assertRedirect('/dashboard');
        $response->assertSessionHas('error');
    }

    /**
     * Helper to create super admin user
     */
    protected function createSuperAdmin(): User
    {
        $user = User::factory()->create(['id' => 1]);
        Admin::create([
            'user_id' => $user->id,
            'name' => 'Super Admin',
            'email' => $user->email,
        ]);

        return $user;
    }
}
