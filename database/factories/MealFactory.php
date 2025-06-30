<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\Meal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory
{
    protected $model = Meal::class;

    public function definition()
    {
        $mealTime = $this->faker->dateTimeBetween('-1 month', 'now');
        $preBg = $this->faker->numberBetween(70, 200);
        $postBg = $preBg + $this->faker->numberBetween(20, 80);
        
        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'meal_type' => $this->faker->randomElement(['breakfast', 'lunch', 'dinner', 'other']),
            'meal_time' => $mealTime,
            'pre_bg' => $preBg,
            'post_bg' => $postBg,
            'post_meal_bg_time' => (clone $mealTime)->modify('+2 hours'),
            'food_desc' => $this->faker->sentence,
            'carbs_grams' => $this->faker->numberBetween(10, 100),
            'sugars_grams' => $this->faker->numberBetween(5, 50),
            'status' => $this->faker->randomElement(['pending', 'completed']),
            'is_override' => false,
            'notes' => $this->faker->optional()->sentence,
        ];
    }
}
