<?php

namespace Tests\Feature\Models;

use App\Models\Child;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_meal()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        $mealTime = now();
        
        $meal = Meal::create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'meal_type' => 'breakfast',
            'meal_time' => $mealTime,
            'pre_bg' => 100,
            'post_bg' => 150,
            'post_meal_bg_time' => $mealTime->copy()->addHours(2),
            'food_desc' => 'Test meal',
            'carbs_grams' => 50,
            'sugars_grams' => 20,
            'status' => 'logged',
            'is_override' => false,
        ]);

        $this->assertInstanceOf(Meal::class, $meal);
        $this->assertEquals('breakfast', $meal->meal_type);
        $this->assertEquals(100, $meal->pre_bg);
        $this->assertEquals('Test meal', $meal->food_desc);
        $this->assertEquals('logged', $meal->status);
        $this->assertFalse($meal->is_override);
    }

    /** @test */
    public function it_has_relationships()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        $meal = Meal::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
        ]);
        
        $this->assertInstanceOf(Child::class, $meal->child);
        $this->assertInstanceOf(User::class, $meal->user);
        $this->assertEquals($child->id, $meal->child->id);
        $this->assertEquals($user->id, $meal->user->id);
    }

    /** @test */
    public function it_can_scope_by_meal_type()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        
        // Create a breakfast meal
        $breakfast = Meal::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'meal_type' => 'breakfast',
            'status' => 'logged'
        ]);
        
        // Create a lunch meal
        $lunch = Meal::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'meal_type' => 'lunch',
            'status' => 'logged'
        ]);
        
        // Test the scope
        $breakfastMeals = Meal::where('meal_type', 'breakfast')->get();
        $this->assertCount(1, $breakfastMeals);
        $this->assertEquals('breakfast', $breakfastMeals->first()->meal_type);
        
        // Test the relationship
        $this->assertCount(2, $child->meals);
        $this->assertTrue($child->meals->contains($breakfast));
        $this->assertTrue($child->meals->contains($lunch));
    }
    
    /** @test */
    public function it_can_filter_by_status()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        
        // Create meals with different statuses
        $loggedMeal = Meal::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'status' => 'logged'
        ]);
        
        $pendingMeal = Meal::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'status' => 'pending'
        ]);
        
        // Test filtering by status
        $loggedMeals = Meal::where('status', 'logged')->get();
        $this->assertCount(1, $loggedMeals);
        $this->assertEquals('logged', $loggedMeals->first()->status);
        
        $pendingMeals = Meal::where('status', 'pending')->get();
        $this->assertCount(1, $pendingMeals);
        $this->assertEquals('pending', $pendingMeals->first()->status);
    }
}
