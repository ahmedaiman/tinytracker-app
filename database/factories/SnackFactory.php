<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\InsulinType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SnackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $snackTime = $this->faker->dateTimeThisMonth();
        
        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'snack_time' => $snackTime,
            'food_description' => $this->faker->sentence(3),
            'carbs_grams' => $this->faker->numberBetween(5, 50),
            'sugars_grams' => function (array $attributes) {
                return $this->faker->numberBetween(0, $attributes['carbs_grams']);
            },
            'pre_snack_bg' => $this->faker->optional(0.8)->numberBetween(70, 250),
            'post_snack_bg' => function (array $attributes) {
                return $attributes['pre_snack_bg'] 
                    ? $this->faker->numberBetween(
                        $attributes['pre_snack_bg'] - 20, 
                        $attributes['pre_snack_bg'] + 80
                    )
                    : null;
            },
            'post_snack_bg_time' => function (array $attributes) use ($snackTime) {
                return $this->faker->optional(0.7)->dateTimeBetween(
                    $snackTime->add(new \DateInterval('PT30M')), // 30 minutes after snack
                    $snackTime->add(new \DateInterval('PT2H'))   // up to 2 hours after snack
                );
            },
            'insulin_type_id' => function () {
                if ($this->faker->boolean(60)) {
                    return InsulinType::inRandomOrder()->first()?->id;
                }
                return null;
            },
            'insulin_units' => function (array $attributes) {
                return $attributes['insulin_type_id'] 
                    ? $this->faker->randomFloat(2, 0.5, 5)
                    : null;
            },
            'insulin_injected_at' => function (array $attributes) use ($snackTime) {
                return $attributes['insulin_type_id'] 
                    ? $this->faker->dateTimeBetween(
                        $snackTime->sub(new \DateInterval('PT15M')), // 15 minutes before snack
                        $snackTime->add(new \DateInterval('PT15M'))  // 15 minutes after snack
                    )
                    : null;
            },
            'notes' => $this->faker->optional(0.3)->paragraph,
            'created_at' => $snackTime,
            'updated_at' => $snackTime,
        ];
    }

    /**
     * Configure the model factory to include insulin data.
     */
    public function withInsulin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'insulin_type_id' => InsulinType::factory(),
                'insulin_units' => $this->faker->randomFloat(2, 0.5, 5),
                'insulin_injected_at' => $this->faker->dateTimeThisMonth(),
            ];
        });
    }

    /**
     * Configure the model factory to include blood glucose data.
     */
    public function withBloodGlucose(): static
    {
        $preBg = $this->faker->numberBetween(70, 250);
        
        return $this->state(function (array $attributes) use ($preBg) {
            return [
                'pre_snack_bg' => $preBg,
                'post_snack_bg' => $this->faker->numberBetween(
                    $preBg - 20, 
                    $preBg + 80
                ),
                'post_snack_bg_time' => function (array $attributes) {
                    return Carbon::parse($attributes['snack_time'])
                        ->addMinutes($this->faker->numberBetween(30, 120));
                },
            ];
        });
    }

    /**
     * Configure the model factory to set a specific child.
     */
    public function forChild(Child $child): static
    {
        return $this->state([
            'child_id' => $child->id,
        ]);
    }

    /**
     * Configure the model factory to set a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state([
            'user_id' => $user->id,
        ]);
    }
}
