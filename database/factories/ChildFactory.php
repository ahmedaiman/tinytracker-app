<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChildFactory extends Factory
{
    protected $model = Child::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name,
            'date_of_birth' => $this->faker->dateTimeBetween('-10 years', '-1 year'),
            'gender' => $this->faker->randomElement(['male', 'female', 'other']),
            'notes' => $this->faker->optional()->paragraph,
        ];
    }
}
