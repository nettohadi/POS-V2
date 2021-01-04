<?php

namespace Tests\Unit\Models;

use App\Models\Ingredient;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_has_ingredients()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $recipe = Recipe::factory()->create();
        $expectedIngredients = Ingredient::factory()->count(3)
                               ->create(['recipe_id' => $recipe->id])->toArray();

        /* 2. Invoke --------------------------------*/
        $ingredients = $recipe->ingredients->toArray();

        /* 3. Assert --------------------------------*/
        $this->assertEquals($expectedIngredients, $ingredients);
    }

    /** @test **/
    public function it_belongs_to_a_product_and_an_outlet()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $expectedProduct = Product::factory()->create()->toArray();
        $expectedOutlet = Outlet::factory()->create()->toArray();
        $recipe = Recipe::factory()->create(['product_id' => $expectedProduct['id'],
                                             'outlet_id' => $expectedOutlet['id']]);


        /* 2. Invoke --------------------------------*/
        $recipe = Recipe::with(['product','outlet'])->first();
        $product = $recipe->product->toArray();
        $outlet = $recipe->outlet->toArray();

        /* 3. Assert --------------------------------*/
        $this->assertEquals($expectedProduct, $product);
        $this->assertEquals($expectedOutlet, $outlet);
    }

    /** @test **/
    public function it_can_be_filtered_by_product_name()
    {
        //create 2 products having name contain the word 'Chicken'
        $friedChicken = Product::factory()->create(['name' => 'fried chicken']);
        $boiledChicken = Product::factory()->create(['name' => 'boiled chicken']);
        //create random outlet
        $outlet = Outlet::factory()->create();

        //create 2 recipes with above product
        $recipeFriedChicken = Recipe::factory()->create(['product_id' => $friedChicken->id,
            'outlet_id' => $outlet->id]);

        $recipeBoiledChicken = Recipe::factory()->create(['product_id' => $boiledChicken->id,
            'outlet_id' => $outlet->id]);

        $expectedRecipes = array($recipeFriedChicken->toArray(), $recipeBoiledChicken->toArray());

        //create 3 random recipes
        Recipe::factory()->count(3)->create(['product_id' =>'random_product']);

        /* 2. Invoke --------------------------------*/
        $recipes = Recipe::filterByProductName('chicken')->get()->toArray();

        /* 3. Assert --------------------------------*/
        $this->assertCount(2,$recipes);
        $this->assertEquals($expectedRecipes, $recipes);
    }

    /** @test **/
    public function it_can_be_filtered_by_outlet_id()
    {

        //create  outlet with id 'AAA'
        $outlet = Outlet::factory()->create(['id' => 'AAA']);

        //create 2 recipes with above product
        $expectedRecipes = Recipe::factory()->count(2)->create(['outlet_id' => $outlet->id])->toArray();

        //create 3 random recipes
        Recipe::factory()->count(3)->create();

        /* 2. Invoke --------------------------------*/
        $recipes = Recipe::filterByOutlet('AAA')->get()->toArray();

        /* 3. Assert --------------------------------*/
        $this->assertCount(2,$recipes);
        $this->assertEquals($expectedRecipes, $recipes);
    }
}
