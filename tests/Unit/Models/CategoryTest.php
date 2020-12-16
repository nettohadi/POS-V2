<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\CreatesApplication;

class CategoryTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    /** @test **/
    public function can_be_filtered_by_name()
    {
        $expectedCategories = [];
        //create two categories which contain the word 'Paket'
        $expectedCategories[] = Category::factory()->create([
            'name' => 'Paket Hemat'
        ])->toArray();

        $expectedCategories[] = Category::factory()->create([
            'name' => 'Paket Geprek'
        ])->toArray();
        //-------------------------------------------------

        //Create 10 random categories
        Category::factory()->count(5)->create();

        /* 2. Invoke ------------------------------ */
        $categories = Category::filterByName('paket')->get()->toArray();

        /* 3. Assert --------------------------------*/
        $this->assertCount(2,$categories);
        $this->assertEquals($expectedCategories, $categories);
    }
}
