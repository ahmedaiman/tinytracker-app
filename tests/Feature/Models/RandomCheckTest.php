<?php

namespace Tests\Feature\Models;

use App\Models\Child;
use App\Models\RandomCheck;
use App\Models\User;
use App\Models\InsulinType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RandomCheckTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_random_check()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        $checkTime = now();
        
        $check = RandomCheck::create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'check_time' => $checkTime,
            'bg_value' => 120,
            'context' => 'fasting',
            'is_manual_entry' => true,
            'notes' => 'Fasting check before breakfast'
        ]);

        $this->assertInstanceOf(RandomCheck::class, $check);
        $this->assertEquals(120, $check->bg_value);
        $this->assertEquals('fasting', $check->context);
        $this->assertTrue($check->is_manual_entry);
        $this->assertEquals('Fasting check before breakfast', $check->notes);
    }

    /** @test */
    public function it_has_relationships()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        $check = RandomCheck::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
        ]);
        
        // Test relationships
        $this->assertInstanceOf(Child::class, $check->child);
        $this->assertInstanceOf(User::class, $check->user);
        $this->assertEquals($child->id, $check->child->id);
        $this->assertEquals($user->id, $check->user->id);
        
        // Test inverse relationships
        $this->assertTrue($child->randomChecks->contains($check));
        $this->assertTrue($user->randomChecks->contains($check));
    }

    /** @test */
    public function it_can_scope_by_context()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        
        // Create checks with different contexts
        $fastingCheck = RandomCheck::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'context' => 'fasting',
            'bg_value' => 95,
            'check_time' => now()->subHours(2)
        ]);
        
        $beforeMealCheck = RandomCheck::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'context' => 'before_meal',
            'bg_value' => 110,
            'check_time' => now()->subHour()
        ]);
        
        // Test filtering by context
        $fastingChecks = RandomCheck::where('context', 'fasting')->get();
        $this->assertCount(1, $fastingChecks);
        $this->assertEquals('fasting', $fastingChecks->first()->context);
        $this->assertEquals(95, $fastingChecks->first()->bg_value);
        
        // Test sorting by check_time
        $recentChecks = RandomCheck::orderBy('check_time', 'desc')->get();
        $this->assertEquals('before_meal', $recentChecks->first()->context);
    }
    
    /** @test */
    public function it_handles_insulin_data()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        $insulinType = InsulinType::factory()->create();
        
        $check = RandomCheck::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'bg_value' => 250,
            'insulin_type_id' => $insulinType->id,
            'insulin_units' => 2.5,
            'insulin_injected_at' => now(),
            'context' => 'after_meal',
            'notes' => 'High BG after lunch, corrected with insulin'
        ]);
        
        $this->assertEquals(250, $check->bg_value);
        $this->assertEquals($insulinType->id, $check->insulin_type_id);
        $this->assertEquals(2.5, $check->insulin_units);
        $this->assertNotNull($check->insulin_injected_at);
        $this->assertEquals('after_meal', $check->context);
        
        // Test relationship with insulin type
        $this->assertInstanceOf(InsulinType::class, $check->insulinType);
        $this->assertEquals($insulinType->id, $check->insulinType->id);
    }
}
