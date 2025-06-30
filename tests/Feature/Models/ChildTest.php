<?php

namespace Tests\Feature\Models;

use App\Models\Child;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChildTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_child()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        
        $child = Child::create([
            'user_id' => $user->id,
            'name' => 'Test Child',
            'date_of_birth' => now()->subYears(5),
            'gender' => 'male',
        ]);

        $this->assertInstanceOf(Child::class, $child);
        $this->assertEquals('Test Child', $child->name);
        $this->assertEquals('male', $child->gender);
        $this->assertEquals($user->id, $child->user_id);
    }

    /** @test */
    public function it_has_a_relationship_with_user()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        
        $this->assertInstanceOf(User::class, $child->user);
        $this->assertEquals($user->id, $child->user->id);
    }

    /** @test */
    public function it_can_calculate_age()
    {
        $child = Child::factory()->create([
            'date_of_birth' => now()->subYears(5)
        ]);
        
        $this->assertIsArray($child->age);
        $this->assertEquals(5, $child->age['years']);
        $this->assertArrayHasKey('display', $child->age);
    }
}
