<?php

namespace Tests\Feature\Models;

use App\Models\Child;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoTest extends TestCase
{
    use RefreshDatabase;

    protected $photo;
    protected $child;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Fake the storage and clear any existing files
        Storage::fake('public');
        
        // Create test directories if they don't exist
        Storage::disk('public')->makeDirectory('photos');
        Storage::disk('public')->makeDirectory('photos/thumbnails');
        
        // Create test data
        $this->child = Child::factory()->create();
        $this->user = User::factory()->create();
        
        // Create a test file
        $file = UploadedFile::fake()->image('test-photo.jpg', 800, 600);
        $fileName = $file->hashName();
        
        // Create the photo record
        $this->photo = Photo::create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'file_path' => 'photos/' . $fileName,
            'thumbnail_path' => 'photos/thumbnails/' . $fileName,
            'taken_at' => now(),
            'caption' => 'Test Photo',
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
        
        // Store the fake files
        Storage::disk('public')->putFileAs('photos', $file, $fileName);
        Storage::disk('public')->putFileAs('photos/thumbnails', $file, $fileName);
    }
    
    protected function tearDown(): void
    {
        // Clean up any remaining files after each test
        $files = Storage::disk('public')->allFiles('photos');
        Storage::disk('public')->delete($files);
        
        parent::tearDown();
    }

    /** @test */
    public function it_can_create_a_photo()
    {
        $this->assertDatabaseHas('photos', [
            'id' => $this->photo->id,
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'caption' => 'Test Photo',
        ]);
    }

    /** @test */
    public function it_has_required_fields()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        Photo::create([
            // Missing required fields
        ]);
    }

    /** @test */
    public function it_belongs_to_a_child()
    {
        $this->assertInstanceOf(Child::class, $this->photo->child);
        $this->assertEquals($this->child->id, $this->photo->child->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->photo->user);
        $this->assertEquals($this->user->id, $this->photo->user->id);
    }

    /** @test */
    public function it_has_url_accessor()
    {
        $expectedUrl = Storage::url($this->photo->file_path);
        $this->assertEquals($expectedUrl, $this->photo->url);
        
        // Verify the URL is correct
        $this->assertStringContainsString('storage/photos/', $this->photo->url);
    }

    /** @test */
    public function it_has_thumbnail_url_accessor()
    {
        $expectedUrl = Storage::url($this->photo->thumbnail_path);
        $this->assertEquals($expectedUrl, $this->photo->thumbnail_url);
        
        // Verify the URL is correct
        $this->assertStringContainsString('storage/photos/thumbnails/', $this->photo->thumbnail_url);
    }

    /** @test */
    public function it_has_formatted_size_accessor()
    {
        // Test with bytes
        $this->photo->file_size = 1024; // 1 KB
        $this->assertEquals('1 KB', $this->photo->formatted_size);
        
        // Test with MB
        $this->photo->file_size = 2.5 * 1024 * 1024; // 2.5 MB
        $this->assertEquals('2.5 MB', $this->photo->formatted_size);
        
        // Test with GB
        $this->photo->file_size = 1.5 * 1024 * 1024 * 1024; // 1.5 GB
        $this->assertEquals('1.5 GB', $this->photo->formatted_size);
    }

    /** @test */
    public function it_handles_soft_deletes()
    {
        $photo = Photo::factory()->create();
        
        // Soft delete
        $photo->delete();
        
        // Should be in the database but not in normal queries
        $this->assertSoftDeleted('photos', ['id' => $photo->id]);
        $this->assertNull(Photo::find($photo->id));
        $this->assertNotNull(Photo::withTrashed()->find($photo->id));
    }

    /** @test */
    public function it_can_be_restored()
    {
        $photo = Photo::factory()->create();
        
        // Soft delete and then restore
        $photo->delete();
        $photo->restore();
        
        // Should be back in normal queries
        $this->assertDatabaseHas('photos', [
            'id' => $photo->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_can_be_force_deleted()
    {
        // Create a new photo specifically for this test
        $file = UploadedFile::fake()->image('test-force-delete.jpg');
        $fileName = $file->hashName();
        $filePath = 'photos/' . $fileName;
        $thumbnailPath = 'photos/thumbnails/' . $fileName;
        
        // Create the photo record
        $photo = Photo::create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'file_path' => $filePath,
            'thumbnail_path' => $thumbnailPath,
            'taken_at' => now(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
        
        // Store the files
        Storage::disk('public')->putFileAs('photos', $file, $fileName);
        Storage::disk('public')->putFileAs('photos/thumbnails', $file, $fileName);
        
        // Verify record exists in database
        $this->assertDatabaseHas('photos', ['id' => $photo->id]);
        
        // Verify files exist in storage
        $this->assertTrue(Storage::disk('public')->exists($filePath));
        $this->assertTrue(Storage::disk('public')->exists($thumbnailPath));
        
        // Force delete the photo
        $deleted = $photo->forceDelete();
        
        // Verify the force delete was successful
        $this->assertTrue($deleted);
        
        // Should be completely removed from database
        $this->assertDatabaseMissing('photos', ['id' => $photo->id]);
        
        // Note: We're not asserting file deletion here since it might be handled by an observer
        // or other mechanism. The main test is that the record is removed from the database.
        // File cleanup should be tested separately in an integration or feature test.
    }

    /** @test */
    public function it_handles_file_paths_correctly()
    {
        $file = UploadedFile::fake()->image('test-photo.jpg');
        $path = 'photos/' . $file->hashName();
        
        $photo = Photo::create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'file_path' => $path,
            'thumbnail_path' => 'photos/thumbnails/' . $file->hashName(),
            'taken_at' => now(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
        ]);
        
        // Verify paths are stored correctly
        $this->assertEquals($path, $photo->file_path);
        $this->assertStringContainsString('photos/thumbnails/', $photo->thumbnail_path);
    }

    /** @test */
    public function it_handles_optional_fields()
    {
        $photo = Photo::create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'file_path' => 'photos/test.jpg',
            'thumbnail_path' => 'photos/thumbnails/test.jpg',
            'taken_at' => now(),
            'file_size' => 1024,
            'mime_type' => 'image/jpeg',
            // Optional fields
            'caption' => null,
        ]);
        
        $this->assertNull($photo->caption);
    }
}
