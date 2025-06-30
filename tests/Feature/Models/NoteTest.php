<?php

namespace Tests\Feature\Models;

use App\Models\Note;
use App\Models\Child;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_note()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        
        $note = Note::create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'title' => 'Important Medical Note',
            'content' => 'This is a detailed note about a medical condition.',
            'type' => 'medical',
            'noted_at' => now(),
            'is_important' => true,
            'tags' => ['medication', 'doctor_visit'],
            'related_data' => [
                'blood_glucose' => 180,
                'medication' => 'Insulin',
                'doctor_name' => 'Dr. Smith',
                'follow_up_needed' => true,
            ],
        ]);

        $this->assertInstanceOf(Note::class, $note);
        $this->assertEquals('Important Medical Note', $note->title);
        $this->assertEquals('medical', $note->type);
        $this->assertTrue($note->is_important);
        $this->assertContains('medication', $note->tags);
        $this->assertEquals(180, $note->related_data['blood_glucose']);
    }

    /** @test */
    public function it_has_relationships()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        
        $note = Note::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
        ]);
        
        $this->assertInstanceOf(Child::class, $note->child);
        $this->assertInstanceOf(User::class, $note->user);
        $this->assertEquals($child->id, $note->child->id);
        $this->assertEquals($user->id, $note->user->id);
    }

    /** @test */
    public function it_can_scope_by_type()
    {
        $user = User::factory()->create(['role' => 'guardian']);
        $child = Child::factory()->create(['user_id' => $user->id]);
        
        // Create notes of different types
        $medicalNote = Note::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'type' => 'medical',
        ]);
        
        $dietNote = Note::factory()->create([
            'child_id' => $child->id,
            'user_id' => $user->id,
            'type' => 'diet',
        ]);
        
        // Test scopes
        $medicalNotes = Note::where('type', 'medical')->get();
        $this->assertCount(1, $medicalNotes);
        $this->assertEquals('medical', $medicalNotes->first()->type);
        
        $dietNotes = Note::where('type', 'diet')->get();
        $this->assertCount(1, $dietNotes);
        $this->assertEquals('diet', $dietNotes->first()->type);
    }

    /** @test */
    public function it_can_manage_tags()
    {
        $note = Note::factory()->create([
            'tags' => ['initial'],
        ]);
        
        // Test adding tags
        $note->addTag('important');
        $note->addTag('follow_up');
        $note->save();
        
        $this->assertTrue($note->hasTag('important'));
        $this->assertTrue($note->hasTag('follow_up'));
        $this->assertTrue($note->hasTag('initial'));
        
        // Test removing a tag
        $note->removeTag('initial');
        $note->save();
        
        $this->assertFalse($note->hasTag('initial'));
        $this->assertTrue($note->hasTag('important'));
    }

    /** @test */
    public function it_has_scope_for_date_range()
    {
        // Use start of day to avoid timezone issues
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $lastWeek = now()->subWeek()->startOfDay();
        
        // Create test notes with specific dates
        $noteToday = Note::factory()->create([
            'noted_at' => $today,
        ]);
        
        $noteYesterday = Note::factory()->create([
            'noted_at' => $yesterday,
        ]);
        
        $noteLastWeek = Note::factory()->create([
            'noted_at' => $lastWeek,
        ]);
        
        // Create another note for today to test inclusive range
        $anotherNoteToday = Note::factory()->create([
            'noted_at' => $today->copy()->addHours(12), // Add some hours to today
        ]);
        
        // Test date range scope (inclusive of start and end dates)
        $recentNotes = Note::dateRange($yesterday, $today)->get();
        $this->assertCount(3, $recentNotes, 'Should include notes from yesterday and today');
        
        // Test date range for older notes (last week to yesterday)
        $olderNotes = Note::dateRange($lastWeek, $yesterday->copy()->subSecond())->get();
        $this->assertCount(1, $olderNotes, 'Should only include the note from last week');
        $this->assertEquals(
            $lastWeek->format('Y-m-d'), 
            $olderNotes->first()->noted_at->startOfDay()->format('Y-m-d'),
            'The note should be from last week'
        );
    }

    /** @test */
    public function it_has_important_scope()
    {
        // Clear existing notes to ensure a clean state
        Note::query()->delete();
        
        // Create 3 important notes and 2 regular ones
        $importantNotes = Note::factory()->count(3)->important()->create();
        $regularNotes = Note::factory()->count(2)->create(['is_important' => false]);
        
        // Verify the counts
        $this->assertCount(3, Note::where('is_important', true)->get(), 'There should be 3 important notes');
        $this->assertCount(2, Note::where('is_important', false)->get(), 'There should be 2 regular notes');
        
        // Verify the important scope
        $this->assertCount(3, Note::important()->get(), 'The important scope should return 3 notes');
    }

    /** @test */
    public function it_has_excerpt_attribute()
    {
        $longContent = str_repeat('This is a long content. ', 20); // About 400 characters
        
        $note = Note::factory()->create([
            'content' => $longContent,
        ]);
        
        $excerpt = $note->excerpt; // Default 100 chars
        $this->assertLessThanOrEqual(100 + 3, strlen($excerpt)); // +3 for the '...'
        $this->assertStringEndsWith('...', $excerpt);
        
        // Test custom length
        $customExcerpt = $note->getExcerptAttribute(50);
        $this->assertLessThanOrEqual(50 + 3, strlen($customExcerpt));
    }

    /** @test */
    public function it_has_type_name_attribute()
    {
        $note = Note::factory()->create(['type' => 'medical']);
        $this->assertEquals('Medical Note', $note->type_name);
        
        $note->type = 'diet';
        $this->assertEquals('Diet Note', $note->type_name);
        
        // Test with unknown type
        $note->type = 'unknown';
        $this->assertEquals('Unknown', $note->type_name);
    }

    /** @test */
    public function it_uses_soft_deletes()
    {
        $note = Note::factory()->create();
        
        $this->assertNull($note->deleted_at);
        
        $note->delete();
        $this->assertSoftDeleted($note);
        
        // Should still exist in database
        $this->assertDatabaseHas('notes', ['id' => $note->id]);
        
        // Force delete
        $note->forceDelete();
        $this->assertDatabaseMissing('notes', ['id' => $note->id]);
    }
}
