<?php

namespace Tests\Unit\Models;

use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\CreatesApplication;

class UnitTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    /** @test **/
    public function can_be_filtered_by_name()
    {
        $expectedUnits = [];

        //create two units which contain the word 'liter'
        $expectedUnits[] = Unit::factory()->create([
            'name' => 'Liter'
        ])->toArray();

        $expectedUnits[] = Unit::factory()->create([
            'name' => 'Mililiter'
        ])->toArray();
        //---------------------------------------------

        //Create 10 random units
        Unit::factory()->count(10)->create();

        /* 2. Invoke ------------------------------ */
        $units = Unit::filterByName('liter')->get()->toArray();

        /* 3. Assert --------------------------------*/
        $this->assertCount(2,$units);
        $this->assertEquals($expectedUnits, $units);
    }
}
