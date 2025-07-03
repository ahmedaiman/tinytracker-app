<?php

namespace Database\Factories;

use App\Models\Child;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class PhotoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Models\Photo::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a fake image file for testing
        $image = UploadedFile::fake()->image('test-photo.jpg');
        $filePath = 'photos/' . $image->hashName();
        $thumbnailPath = 'photos/thumbnails/' . $image->hashName();
        
        // Store the fake files in the storage
        Storage::disk('public')->put($filePath, file_get_contents($image));
        Storage::disk('public')->put($thumbnailPath, file_get_contents($image));
        
        return [
            'child_id' => Child::factory(),
            'user_id' => User::factory(),
            'file_path' => $filePath,
            'thumbnail_path' => $thumbnailPath,
            'mime_type' => $image->getMimeType(),
            'file_size' => $image->getSize(),
            'caption' => $this->faker->optional()->sentence(),
            'taken_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
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

    /**
     * Configure the model factory to set a specific date.
     */
    public function takenAt($date): static
    {
        return $this->state([
            'taken_at' => $date,
        ]);
    }

    /**
     * Configure the model factory to set a specific description.
     */
    public function withDescription(string $description): static
    {
        return $this->state([
            'description' => $description,
        ]);
    }
}
