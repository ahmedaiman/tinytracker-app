<?php

namespace Database\Factories;

use App\Models\InsulinType;
use Illuminate\Database\Eloquent\Factories\Factory;

class InsulinTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InsulinType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'onset' => $this->faker->numberBetween(5, 30) . ' minutes',
            'peak' => $this->faker->numberBetween(1, 8) . ' hours',
            'duration' => $this->faker->numberBetween(12, 24) . ' hours',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
