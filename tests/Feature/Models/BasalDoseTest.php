<?php

namespace Tests\Feature\Models;

use App\Models\BasalDose;
use App\Models\Child;
use App\Models\InsulinType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasalDoseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->child = Child::factory()->create(['user_id' => $this->user->id]);
        $this->insulinType = InsulinType::factory()->create();
    }

    /** @test */
    public function it_can_create_a_basal_dose()
    {
        $dose = BasalDose::create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
            'units' => 5.5,
            'injection_date' => now()->toDateString(),
            'notes' => 'Evening basal dose',
            'is_manual_entry' => false,
        ]);

        $this->assertNotNull($dose->id);
        $this->assertDatabaseHas('basal_doses', [
            'id' => $dose->id,
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
            'units' => 5.5,
            'notes' => 'Evening basal dose',
            'is_manual_entry' => 0,
        ]);
    }

    /** @test */
    public function it_has_required_fields()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        BasalDose::create([
            'units' => 5.5,
            'injected_at' => now(),
        ]);
    }

    /** @test */
    public function it_belongs_to_a_child()
    {
        $dose = BasalDose::factory()->create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
        ]);

        $this->assertInstanceOf(Child::class, $dose->child);
        $this->assertEquals($this->child->id, $dose->child->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $dose = BasalDose::factory()->create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
        ]);

        $this->assertInstanceOf(User::class, $dose->user);
        $this->assertEquals($this->user->id, $dose->user->id);
    }

    /** @test */
    public function it_belongs_to_an_insulin_type()
    {
        $dose = BasalDose::factory()->create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
        ]);

        $this->assertInstanceOf(InsulinType::class, $dose->insulinType);
        $this->assertEquals($this->insulinType->id, $dose->insulinType->id);
    }

    /** @test */
    public function it_has_scope_for_specific_child()
    {
        $anotherChild = Child::factory()->create(['user_id' => $this->user->id]);
        
        $dose1 = BasalDose::factory()->create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
        ]);
        
        $dose2 = BasalDose::factory()->create([
            'child_id' => $anotherChild->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
        ]);

        $filteredDoses = BasalDose::forChild($this->child->id)->get();
        
        $this->assertCount(1, $filteredDoses);
        $this->assertEquals($dose1->id, $filteredDoses->first()->id);
    }

    /** @test */
    public function it_has_scope_for_specific_date()
    {
        $today = now();
        $yesterday = now()->subDay();

        // Create doses for today and yesterday
        $doseToday = BasalDose::factory()->create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
            'injection_date' => $today->toDateString(),
        ]);

        $doseYesterday = BasalDose::factory()->create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
            'injection_date' => $yesterday->toDateString(),
        ]);

        // Test scope
        $todaysDoses = BasalDose::forDate($today)->get();
        $this->assertCount(1, $todaysDoses);
        $this->assertTrue($todaysDoses->contains($doseToday));
        $this->assertFalse($todaysDoses->contains($doseYesterday));
    }

    /** @test */
    public function it_has_scope_for_correction_doses()
    {
        // Create a unique child for this test to avoid unique constraint violation
        $child = \App\Models\Child::factory()->create();
        
        $correctionDose = BasalDose::factory()->create([
            'child_id' => $child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
            'is_correction_dose' => true,
            'injection_date' => now()->toDateString(),
        ]);

        $regularDose = BasalDose::factory()->create([
            'child_id' => $child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
            'is_correction_dose' => false,
            'injection_date' => now()->addDay()->toDateString(),
        ]);

        // Test scope
        $correctionDoses = BasalDose::correctionDoses()->get();
        $this->assertTrue($correctionDoses->contains($correctionDose));
        $this->assertFalse($correctionDoses->contains($regularDose));
    }

    /** @test */
    public function it_has_formatted_date_accessor()
    {
        $date = now();
        $dose = BasalDose::factory()->create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
            'injection_date' => $date->toDateString(),
        ]);

        $this->assertEquals($date->format('M j, Y'), $dose->formatted_date);
    }

    /** @test */
    public function it_has_dose_with_units_accessor()
    {
        $dose = BasalDose::factory()->create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
            'units' => 4.5,
        ]);

        $this->assertEquals('4.50 units', $dose->dose_with_units);
    }

    /** @test */
    public function it_has_days_since_injection_accessor()
    {
        $injectionDate = now()->subDay();
        $dose = BasalDose::factory()->create([
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
            'insulin_type_id' => $this->insulinType->id,
            'injection_date' => $injectionDate->toDateString(),
        ]);

        // The actual format might vary based on the time of day, so we'll just check that it contains 'day'
        $this->assertStringContainsString('day', $dose->days_since_injection);
    }
}
