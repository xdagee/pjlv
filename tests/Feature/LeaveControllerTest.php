<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Staff;
use App\User;

class LeaveControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test unauthenticated users are redirected to login.
     */
    public function test_unauthenticated_users_redirected(): void
    {
        $response = $this->get('/leaves');
        $response->assertRedirect('/login');
    }

    /**
     * Test dashboard loads for authenticated users.
     */
    public function test_dashboard_loads_for_authenticated_users(): void
    {
        $user = User::first();

        if ($user) {
            $response = $this->actingAs($user)->get('/dashboard');
            $response->assertStatus(200);
        } else {
            $this->markTestSkipped('No user found');
        }
    }

    /**
     * Test leaves page loads for authenticated users.
     */
    public function test_leaves_page_loads_for_authenticated_users(): void
    {
        $user = User::first();

        if ($user) {
            $response = $this->actingAs($user)->get('/leaves');
            $response->assertStatus(200);
        } else {
            $this->markTestSkipped('No user found');
        }
    }

    /**
     * Test leave apply page loads for authenticated users.
     */
    public function test_leave_apply_page_loads(): void
    {
        $user = User::first();

        if ($user) {
            $response = $this->actingAs($user)->get('/leaves/apply');
            $response->assertStatus(200);
        } else {
            $this->markTestSkipped('No user found');
        }
    }
}
