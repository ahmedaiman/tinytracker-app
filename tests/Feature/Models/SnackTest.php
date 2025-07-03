<?php

namespace Tests\Feature\Models;

use App\Models\Child;
use App\Models\InsulinType;
use App\Models\Snack;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SnackTest extends TestCase
{
    use RefreshDatabase;

    protected $snack;
    protected $child;
    protected $user;
    protected $insulinType;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->child = Child::factory()->create();
        $this->user = User::factory()->create();
        $this->insulinType = InsulinType::factory()->create();
        
        $this->snack = Snack::factory()
            ->forChild($this->child)
            ->forUser($this->user)
            ->create([
                'insulin_type_id' => $this->insulinType->id,
                'insulin_units' => 2.5,
                'pre_snack_bg' => 120,
                'post_snack_bg' => 180,
                'post_snack_bg_time' => now()->addHour(),
            ]);
    }

    /** @test */
    public function it_can_create_a_snack()
    {
        $this->assertDatabaseHas('snacks', [
            'id' => $this->snack->id,
            'child_id' => $this->child->id,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_has_required_fields()
    {
        $this->expectException('Illuminate\Database\QueryException');
        
        Snack::create([
            // Missing required fields
        ]);
    }

    /** @test */
    public function it_belongs_to_a_child()
    {
        $this->assertInstanceOf(Child::class, $this->snack->child);
        $this->assertEquals($this->child->id, $this->snack->child->id);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $this->assertInstanceOf(User::class, $this->snack->user);
        $this->assertEquals($this->user->id, $this->snack->user->id);
    }

    /** @test */
    public function it_belongs_to_an_insulin_type()
    {
        $this->assertInstanceOf(InsulinType::class, $this->snack->insulinType);
        $this->assertEquals($this->insulinType->id, $this->snack->insulinType->id);
    }

    /** @test */
    public function it_has_scope_for_specific_child()
    {
        // Create another child and snack
        $anotherChild = Child::factory()->create();
        $anotherSnack = Snack::factory()->forChild($anotherChild)->create();
        
        // Test scope
        $snacks = Snack::forChild($this->child->id)->get();
        
        $this->assertCount(1, $snacks);
        $this->assertEquals($this->snack->id, $snacks->first()->id);
        $this->assertNotContains($anotherSnack->id, $snacks->pluck('id'));
    }

    /** @test */
    public function it_has_scope_for_specific_date()
    {
        $today = now();
        $yesterday = now()->subDay();
        
        // Create snacks for different dates
        $todaySnack = Snack::factory()->create(['snack_time' => $today]);
        $yesterdaySnack = Snack::factory()->create(['snack_time' => $yesterday]);
        
        // Test scope with Carbon instance
        $todaysSnacks = Snack::forDate($today)->get();
        $this->assertTrue($todaysSnacks->contains('id', $todaySnack->id));
        $this->assertFalse($todaysSnacks->contains('id', $yesterdaySnack->id));
        
        // Test scope with date string
        $yesterdaysSnacks = Snack::forDate($yesterday->toDateString())->get();
        $this->assertTrue($yesterdaysSnacks->contains('id', $yesterdaySnack->id));
        $this->assertFalse($yesterdaysSnacks->contains('id', $todaySnack->id));
    }

    /** @test */
    public function it_has_formatted_time_accessor()
    {
        $time = now()->setTime(14, 30); // 2:30 PM
        $snack = Snack::factory()->create(['snack_time' => $time]);
        
        $this->assertEquals('02:30 PM', $snack->formatted_time);
    }

    /** @test */
    public function it_checks_if_has_blood_glucose_data()
    {
        // Has BG data
        $withBg = Snack::factory()->create([
            'pre_snack_bg' => 100,
            'post_snack_bg' => 150
        ]);
        $this->assertTrue($withBg->hasBloodGlucoseData());
        
        // Has only pre-snack BG
        $onlyPreBg = Snack::factory()->create([
            'pre_snack_bg' => 100,
            'post_snack_bg' => null
        ]);
        $this->assertTrue($onlyPreBg->hasBloodGlucoseData());
        
        // Has only post-snack BG
        $onlyPostBg = Snack::factory()->create([
            'pre_snack_bg' => null,
            'post_snack_bg' => 150
        ]);
        $this->assertTrue($onlyPostBg->hasBloodGlucoseData());
        
        // No BG data
        $noBg = Snack::factory()->create([
            'pre_snack_bg' => null,
            'post_snack_bg' => null
        ]);
        $this->assertFalse($noBg->hasBloodGlucoseData());
    }

    /** @test */
    public function it_checks_if_has_insulin_data()
    {
        // Has insulin data
        $withInsulin = Snack::factory()->create([
            'insulin_type_id' => $this->insulinType->id,
            'insulin_units' => 2.5
        ]);
        $this->assertTrue($withInsulin->hasInsulinData());
        
        // Missing insulin type
        $noType = Snack::factory()->create([
            'insulin_type_id' => null,
            'insulin_units' => 2.5
        ]);
        $this->assertFalse($noType->hasInsulinData());
        
        // Missing insulin units
        $noUnits = Snack::factory()->create([
            'insulin_type_id' => $this->insulinType->id,
            'insulin_units' => null
        ]);
        $this->assertFalse($noUnits->hasInsulinData());
        
        // No insulin data
        $noInsulin = Snack::factory()->create([
            'insulin_type_id' => null,
            'insulin_units' => null
        ]);
        $this->assertFalse($noInsulin->hasInsulinData());
    }

    /** @test */
    public function it_calculates_post_snack_bg_time_difference()
    {
        $snackTime = now();
        $postBgTime = $snackTime->copy()->addMinutes(45);
        
        $snack = Snack::factory()->create([
            'snack_time' => $snackTime,
            'post_snack_bg_time' => $postBgTime,
        ]);
        
        $this->assertEquals(45, $snack->post_snack_bg_time_difference);
        
        // Test with missing post-snack BG time
        $noPostBg = Snack::factory()->create([
            'snack_time' => $snackTime,
            'post_snack_bg_time' => null,
        ]);
        
        $this->assertNull($noPostBg->post_snack_bg_time_difference);
    }

    /** @test */
    public function it_handles_optional_fields_correctly()
    {
        $snack = Snack::factory()->create([
            'pre_snack_bg' => null,
            'post_snack_bg' => null,
            'post_snack_bg_time' => null,
            'insulin_type_id' => null,
            'insulin_units' => null,
            'insulin_injected_at' => null,
            'notes' => null,
        ]);
        
        $this->assertDatabaseHas('snacks', [
            'id' => $snack->id,
            'pre_snack_bg' => null,
            'post_snack_bg' => null,
            'post_snack_bg_time' => null,
            'insulin_type_id' => null,
            'insulin_units' => null,
            'insulin_injected_at' => null,
            'notes' => null,
        ]);
    }
}
