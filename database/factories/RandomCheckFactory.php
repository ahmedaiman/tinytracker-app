<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\RandomCheck;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RandomCheckFactory extends Factory
{
    protected $model = RandomCheck::class;

    public function definition()
    {
        $checkTime = $this->faker->dateTimeBetween('-1 month', 'now');
        $bgValue = $this->faker->numberBetween(70, 300);
        $context = $this->faker->randomElement([
            'fasting', 'before_meal', 'after_meal', 'before_sleep', 'night', 'other'
        ]);
        
        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'check_time' => $checkTime,
            'bg_value' => $bgValue,
            'context' => $context,
            'notes' => $this->faker->optional()->sentence,
            'is_manual_entry' => $this->faker->boolean,
            'is_high_alert' => $bgValue > 200 ? $this->faker->boolean(80) : $this->faker->boolean(5),
            'is_low_alert' => $bgValue < 70 ? $this->faker->boolean(80) : $this->faker->boolean(5),
        ];
    }
}
