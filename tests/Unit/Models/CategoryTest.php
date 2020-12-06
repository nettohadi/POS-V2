<?php

namespace Tests\Unit;

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

        Category::factory()->create([
            'name' => 'Paket Hemat'
        ]);
        Category::factory()->create([
            'name' => 'Paket Geprek'
        ]);

        //Create 10 random categories
        Category::factory()->count(10)->create();

        /* 2. Invoke ------------------------------ */
        $categories = Category::filterByName('paket')->get();

        /* 3. Assert --------------------------------*/
        $this->assertCount(2,$categories);
    }
}
