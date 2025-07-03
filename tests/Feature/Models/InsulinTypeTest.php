<?php

namespace Tests\Feature\Models;

use App\Models\InsulinType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InsulinTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_an_insulin_type()
    {
        $insulinType = InsulinType::factory()->create([
            'name' => 'Rapid-Acting',
            'description' => 'Rapid-acting insulin starts working within 15 minutes',
            'onset' => '15 minutes',
            'peak' => '1 hour',
            'duration' => '4 hours',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('insulin_types', [
            'name' => 'Rapid-Acting',
            'description' => 'Rapid-acting insulin starts working within 15 minutes',
            'onset' => '15 minutes',
            'peak' => '1 hour',
            'duration' => '4 hours',
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_has_required_fields()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        InsulinType::create([
            // Missing required fields
        ]);
    }

    /** @test */
    public function it_has_name_as_required()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        InsulinType::factory()->create([
            'name' => null,
        ]);
    }

    /** @test */
    public function it_has_unique_name()
    {
        InsulinType::factory()->create(['name' => 'Rapid-Acting']);
        
        $this->expectException('Illuminate\Database\QueryException');
        
        InsulinType::factory()->create(['name' => 'Rapid-Acting']);
    }

    /** @test */
    public function it_can_be_inactive()
    {
        $insulinType = InsulinType::factory()->create([
            'is_active' => false,
        ]);
        
        $this->assertFalse($insulinType->is_active);
    }

    /** @test */
    public function it_has_active_scope()
    {
        // Clear existing data to avoid unique constraint violations
        InsulinType::query()->delete();
        
        // Create 3 active and 2 inactive insulin types with unique names
        InsulinType::factory()->count(3)->create(['is_active' => true, 'name' => fn() => 'Active ' . uniqid()]);
        InsulinType::factory()->count(2)->create(['is_active' => false, 'name' => fn() => 'Inactive ' . uniqid()]);
        
        $activeCount = InsulinType::active()->count();
        $this->assertEquals(3, $activeCount);
    }

    /** @test */
    public function it_has_inactive_scope()
    {
        // Clear existing data to avoid unique constraint violations
        InsulinType::query()->delete();
        
        // Create 3 active and 2 inactive insulin types with unique names
        InsulinType::factory()->count(3)->create(['is_active' => true, 'name' => fn() => 'Active ' . uniqid()]);
        InsulinType::factory()->count(2)->create(['is_active' => false, 'name' => fn() => 'Inactive ' . uniqid()]);
        
        $inactiveCount = InsulinType::inactive()->count();
        $this->assertEquals(2, $inactiveCount);
    }

    /** @test */
    public function it_can_toggle_active_status()
    {
        $insulinType = InsulinType::factory()->create(['is_active' => true]);
        
        // Toggle off
        $insulinType->update(['is_active' => false]);
        $this->assertFalse($insulinType->fresh()->is_active);
        
        // Toggle on
        $insulinType->update(['is_active' => true]);
        $this->assertTrue($insulinType->fresh()->is_active);
    }

    /** @test */
    public function it_has_formatted_duration()
    {
        $insulinType = InsulinType::factory()->create([
            'name' => 'Long-Acting',
            'duration' => '24 hours',
        ]);
        
        $this->assertEquals('24 hours', $insulinType->duration);
    }

    /** @test */
    public function it_can_be_soft_deleted()
    {
        $insulinType = InsulinType::factory()->create();
        
        // Soft delete
        $insulinType->delete();
        
        // Should be in the database but not in normal queries
        $this->assertSoftDeleted('insulin_types', ['id' => $insulinType->id]);
        $this->assertNull(InsulinType::find($insulinType->id));
        $this->assertNotNull(InsulinType::withTrashed()->find($insulinType->id));
    }

    /** @test */
    public function it_can_be_restored()
    {
        $insulinType = InsulinType::factory()->create();
        
        // Soft delete and then restore
        $insulinType->delete();
        $insulinType->restore();
        
        // Should be back in normal queries
        $this->assertDatabaseHas('insulin_types', [
            'id' => $insulinType->id,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function it_has_basal_doses_relationship()
    {
        $insulinType = InsulinType::factory()->create();
        $basalDose = \App\Models\BasalDose::factory()->create([
            'insulin_type_id' => $insulinType->id
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $insulinType->basalDoses);
        $this->assertTrue($insulinType->basalDoses->contains($basalDose));
    }

    /** @test */
    public function it_has_meals_relationship()
    {
        $insulinType = InsulinType::factory()->create();
        $meal = \App\Models\Meal::factory()->create([
            'insulin_type_id' => $insulinType->id
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $insulinType->meals);
        $this->assertTrue($insulinType->meals->contains($meal));
    }

    /** @test */
    public function it_has_snacks_relationship()
    {
        $insulinType = InsulinType::factory()->create();
        $snack = \App\Models\Snack::factory()->create([
            'insulin_type_id' => $insulinType->id
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $insulinType->snacks);
        $this->assertTrue($insulinType->snacks->contains($snack));
    }

    /** @test */
    public function it_has_random_checks_relationship()
    {
        $insulinType = InsulinType::factory()->create();
        $randomCheck = \App\Models\RandomCheck::factory()->create([
            'insulin_type_id' => $insulinType->id
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $insulinType->randomChecks);
        $this->assertTrue($insulinType->randomChecks->contains($randomCheck));
    }
}
