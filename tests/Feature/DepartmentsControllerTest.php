<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DepartmentsTableSeeder::class);
    }

    /** @test */
    public function super_admin_can_view_departments_page()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)->get('/admin/departments');

        $response->assertStatus(200);
        $response->assertViewIs('admin.departments.index');
    }

    /** @test */
    public function super_admin_can_get_departments_json()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)
            ->getJson('/admin/departments');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
        $this->assertEquals(7, count($response->json('data'))); // 7 seeded departments
    }

    /** @test */
    public function super_admin_can_create_department()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)
            ->postJson('/admin/departments', [
                'name' => 'New Department',
                'description' => 'Department Description',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Department created successfully!',
        ]);

        $this->assertDatabaseHas('departments', [
            'name' => 'New Department',
            'description' => 'Department Description',
        ]);
    }

    /** @test */
    public function cannot_create_duplicate_department()
    {
        $admin = $this->createSuperAdmin();

        $response = $this->actingAs($admin)
            ->postJson('/admin/departments', [
                'name' => 'Engineering', // Already seeded
                'description' => 'Duplicate',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function super_admin_can_update_department()
    {
        $admin = $this->createSuperAdmin();
        $dept = Department::first();

        $response = $this->actingAs($admin)
            ->putJson("/admin/departments/{$dept->id}", [
                'name' => 'Updated Name',
                'description' => 'Updated Description',
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('departments', [
            'id' => $dept->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function super_admin_can_delete_unused_department()
    {
        $admin = $this->createSuperAdmin();
        $dept = Department::create([
            'name' => 'Deletable Dept',
            'description' => 'Can be deleted',
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson("/admin/departments/{$dept->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('departments', ['id' => $dept->id]);
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
