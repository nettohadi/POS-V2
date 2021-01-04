<?php

namespace Tests\Feature\Controllers;

use App\Models\Outlet;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\CreatesApplication;
use Tests\TestCase;

class RecipeControllerTest extends TestCase
{
    use RefreshDatabase;

    public $tableName = 'recipes';
    public $routeName = 'recipes';
    public $paramName = 'recipe';

    /** @test **/
    public function recipes_can_be_retrieved_using_pagination()
    {
        /* 1. Setup --------------------------------*/
        $this->withAuthorization();

        $product = Product::factory()->create();
        $outlet = Outlet::factory()->create();

        $recipes = Recipe::factory()->count(7)
                            ->create(['product_id' => $product->id, 'outlet_id' => $outlet->id])
                            ->load('product','outlet')->take(5)->toArray();


        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('recipes.index').'?perPage=5');

        /* 3. Assert --------------------------------*/
        $response->assertOk();

        $data = $response->json('data');
        $this->assertEquals($recipes, $data);
        $this->assertCount(5, $data);
    }

    /** @test **/
    public function recipes_can_be_filtered_by_product_name_and_outlet()
    {
        /* 1. Setup --------------------------------*/
        $this->withAuthorization();
        //create 2 products having name contain the word 'Chicken'
        $friedChicken = Product::factory()->create(['name' => 'fried chicken']);
        $boiledChicken = Product::factory()->create(['name' => 'boiled chicken']);

        //create outlet having having id 'AAA'
        $outletAAA = Outlet::factory()->create(['id'=>'AAA']);

        //create outlet having having id 'BBB'
        $outletBBB = Outlet::factory()->create(['id'=>'BBB']);

        //create 2 recipes with above product
        $recipeFriedChicken = Recipe::factory()->create(['product_id' => $friedChicken->id,
                                                        'outlet_id' => $outletAAA->id])
                                               ->load('product','outlet');

        $recipeFChicken = Recipe::factory()->create(['product_id' => $boiledChicken->id,
                                                        'outlet_id' => $outletBBB->id])
                                                ->load('product','outlet');

        $expectedRecipes = array($recipeFriedChicken->toArray());


        //create 3 random recipes
        Recipe::factory()->count(3)->create(['product_id' =>'random_product']);

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('recipes.index').'?product=chicken&outlet=AAA');

        /* 3. Assert --------------------------------*/
        $response->assertOk();
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($expectedRecipes, $data);
    }
}
