<?php

namespace Database\Factories;

use App\Models\BasalDose;
use App\Models\Child;
use App\Models\InsulinType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BasalDoseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BasalDose::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'insulin_type_id' => InsulinType::factory(),
            'units' => $this->faker->randomFloat(1, 0.5, 10),
            'injection_date' => now()->toDateString(),
            'notes' => $this->faker->boolean(30) ? $this->faker->sentence : null,
            'is_manual_entry' => $this->faker->boolean,
            'is_correction_dose' => $this->faker->boolean(20),
        ];
    }

    /**
     * Indicate that the dose is a correction dose.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function correctionDose()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_correction_dose' => true,
            ];
        });
    }

    /**
     * Indicate that the dose was manually entered.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function manualEntry()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_manual_entry' => true,
            ];
        });
    }

    /**
     * Set the injection site.
     *
     * @param  string  $site
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withInjectionSite($site)
    {
        return $this->state(function (array $attributes) use ($site) {
            return [
                'injection_site' => $site,
            ];
        });
    }
}
