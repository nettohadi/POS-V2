<?php

namespace Tests\Unit;

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

        Unit::factory()->create([
            'name' => 'Paket Hemat'
        ]);

        Unit::factory()->create([
            'name' => 'Paket Geprek'
        ]);

        //Create 10 random units
        Unit::factory()->count(10)->create();

        /* 2. Invoke ------------------------------ */
        $units = Unit::filterByName('paket')->get();

        /* 3. Assert --------------------------------*/
        $this->assertCount(2,$units);
    }
}
