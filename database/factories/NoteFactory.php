<?php

namespace Database\Factories;

use App\Models\Note;
use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class NoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Note::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $notedAt = $this->faker->dateTimeBetween('-6 months', 'now');
        $types = array_keys(Note::TYPES);
        $type = $this->faker->randomElement($types);
        
        // Common tags for different note types
        $tagsByType = [
            'medical' => ['medication', 'doctor_visit', 'symptoms', 'hospital'],
            'diet' => ['breakfast', 'lunch', 'dinner', 'snack', 'carbs', 'sugar'],
            'exercise' => ['walking', 'running', 'sports', 'activity'],
            'symptoms' => ['high_bg', 'low_bg', 'fever', 'nausea', 'headache'],
            'general' => ['reminder', 'observation', 'question'],
            'other' => ['misc', 'follow_up', 'important']
        ];
        
        // Get appropriate tags based on note type
        $availableTags = $tagsByType[$type] ?? $tagsByType['general'];
        $tags = $this->faker->randomElements($availableTags, $this->faker->numberBetween(1, 3));
        
        // Generate related data based on note type
        $relatedData = [];
        if ($type === 'medical') {
            $relatedData = [
                'blood_glucose' => $this->faker->optional(0.7)->numberBetween(70, 300),
                'medication' => $this->faker->optional(0.6)->word,
                'doctor_name' => $this->faker->optional(0.5)->name,
                'follow_up_needed' => $this->faker->boolean(30),
            ];
        } elseif ($type === 'diet') {
            $relatedData = [
                'carbs_grams' => $this->faker->optional(0.8)->numberBetween(10, 150),
                'sugar_grams' => $this->faker->optional(0.8)->randomFloat(1, 0, 50),
                'meal_type' => $this->faker->randomElement(['breakfast', 'lunch', 'dinner', 'snack']),
                'bg_before' => $this->faker->optional(0.5)->numberBetween(70, 200),
                'bg_after' => $this->faker->optional(0.3)->numberBetween(70, 300),
            ];
        } elseif ($type === 'exercise') {
            $relatedData = [
                'duration_minutes' => $this->faker->numberBetween(10, 120),
                'intensity' => $this->faker->randomElement(['light', 'moderate', 'intense']),
                'bg_before' => $this->faker->optional(0.6)->numberBetween(70, 200),
                'bg_after' => $this->faker->optional(0.5)->numberBetween(70, 300),
            ];
        } elseif ($type === 'symptoms') {
            $relatedData = [
                'symptoms' => $this->faker->words($this->faker->numberBetween(1, 4)),
                'severity' => $this->faker->randomElement(['mild', 'moderate', 'severe']),
                'blood_glucose' => $this->faker->optional(0.7)->numberBetween(40, 400),
                'action_taken' => $this->faker->optional(0.8)->sentence,
            ];
        }
        
        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'content' => $this->faker->paragraphs($this->faker->numberBetween(1, 4), true),
            'type' => $type,
            'noted_at' => $notedAt,
            'is_important' => $this->faker->boolean(20), // 20% chance of being important
            'tags' => $tags,
            'related_data' => $relatedData,
            'created_at' => $notedAt,
            'updated_at' => $this->faker->dateTimeBetween($notedAt, 'now'),
        ];
    }
    
    /**
     * Indicate that the note is of a specific type.
     *
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function type(string $type)
    {
        return $this->state(function (array $attributes) use ($type) {
            return [
                'type' => $type,
            ];
        });
    }
    
    /**
     * Indicate that the note is important.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function important()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_important' => true,
            ];
        });
    }
    
    /**
     * Indicate that the note has specific tags.
     *
     * @param  array  $tags
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withTags(array $tags)
    {
        return $this->state(function (array $attributes) use ($tags) {
            return [
                'tags' => $tags,
            ];
        });
    }
    
    /**
     * Indicate that the note is from a specific date.
     *
     * @param  string|\DateTime  $date
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function onDate($date)
    {
        $date = $date instanceof \DateTime ? $date : Carbon::parse($date);
        
        return $this->state(function (array $attributes) use ($date) {
            return [
                'noted_at' => $date,
                'created_at' => $date,
                'updated_at' => $date,
            ];
        });
    }
}
