<?php

namespace Tests\Unit\Models;

use App\Models\Outlet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletTest extends TestCase
{
    use RefreshDatabase;
    /** @test **/
    public function outlets_can_be_filtered_by_name()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $expectedOutlets = [];
        //create two outlet which contain word 'sura'
        $expectedOutlets[] = Outlet::factory()->create(['name' => 'Outlet surabaya'])->toArray();
        $expectedOutlets[] = Outlet::factory()->create(['name' => 'Outlet suramadu'])->toArray();
        //-------------------------------------------
        //create 5 random outlet
        Outlet::factory()->count(5)->create();

        /* 2. Invoke --------------------------------*/
        $outlets = Outlet::filterByName('sura')->get()->toArray();

        /* 3. Assert --------------------------------*/
        $this->assertCount(2,$outlets);
        $this->assertEquals($expectedOutlets, $outlets);
    }

    /** @test **/
    public function outlet_id_should_be_capitalized_automatically()
    {
        /* 1. Setup --------------------------------*/

        /* 2. Invoke --------------------------------*/
        $outlet = Outlet::factory()->create(['id' => 'abc']);

        /* 3. Assert --------------------------------*/
        $this->assertEquals('ABC', $outlet->id);
    }
}
