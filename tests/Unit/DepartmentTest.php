<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Department;
use App\Models\Staff;

class DepartmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_department_can_be_created()
    {
        $dept = Department::create([
            'name' => 'Innovation',
            'description' => 'R&D',
        ]);

        $this->assertDatabaseHas('departments', ['name' => 'Innovation']);
        $this->assertEquals('Innovation', $dept->name);
    }

    public function test_staff_belongs_to_department()
    {
        $dept = Department::create(['name' => 'Sales']);

        $staff = Staff::factory()->create([
            'firstname' => 'Jane',
            'department_id' => $dept->id,
        ]);

        $this->assertInstanceOf(Department::class, $staff->department);
        $this->assertEquals($dept->id, $staff->department->id);
    }
}
