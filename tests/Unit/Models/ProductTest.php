<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    public function can_be_filtered_by_name()
    {
        $expectedProducts = [];

        //create two units which contain the word 'liter'
        $expectedProducts[] = Product::factory()->create([
            'name' => 'Liter'
        ])->toArray();

        $expectedProducts[] = Product::factory()->create([
            'name' => 'Mililiter'
        ])->toArray();
        //---------------------------------------------

        //Create 10 random units
        Product::factory()->count(10)->create();

        /* 2. Invoke ------------------------------ */
        $products = Product::filterByName('liter')->get()->toArray();

        /* 3. Assert --------------------------------*/
        $this->assertCount(2,$products);
        $this->assertEquals($expectedProducts, $products);
    }

    /** @test **/
    public function id_is_set_automatically_if_id_is_empty()
    {
        /* 1. Setup --------------------------------*/

        /* 2. Invoke --------------------------------*/
        $product = Product::factory()->create(['id'=> null]);

        /* 3. Assert --------------------------------*/
        $this->assertNotNull($product->id);
        $this->assertDatabaseHas('products',['id'=>$product->id]);
    }

    /** @test **/
    public function id_is_set_to_a_given_id_if_not_empty()
    {
        /* 1. Setup --------------------------------*/

        /* 2. Invoke --------------------------------*/
        $product = Product::factory()->create(['id'=> 'XXXX']);

        /* 3. Assert --------------------------------*/
        $this->assertEquals('XXXX',$product->id);
        $this->assertDatabaseHas('products',['id'=>'XXXX']);
    }

    /**
     * @test
     *
     * Relationship test
     **/
    public function a_product_belongs_to_unit()
    {
        /* 1. Setup --------------------------------*/
        $unit = Unit::factory()->create();

        /* 2. Invoke --------------------------------*/
        $product = Product::factory()->create(['unit_id' => $unit->id]);

        /* 3. Assert --------------------------------*/
        $this->assertEquals($unit->name, $product->unit->name);
    }

    /**
     * @test
     *
     * Relationship test
     **/
    public function a_product_belongs_to_category()
    {
        /* 1. Setup --------------------------------*/
        $category = Category::factory()->create();

        /* 2. Invoke --------------------------------*/
        $product = Product::factory()->create(['category_id' => $category->id]);

        /* 3. Assert --------------------------------*/
        $this->assertEquals($category->name, $product->category->name);
    }

    /**
     * @test
     *
     * should return image name with unique timestamp
     **/
    public function it_return_unique_image_name_with_timestamp_microsecond()
    {
        /* 1. Setup --------------------------------*/
        $keyword = 'product';
        $imageExtension = 'jpg';
        $timestamp = (string)Carbon::now()->timestamp;
        $microsecond = (string)Carbon::now()->microsecond;

        /* 2. Invoke --------------------------------*/
        $uniqueName = Product::generateImageName($keyword,$imageExtension);

        /* 3. Assert --------------------------------*/
        $this->assertStringContainsString('product_',$uniqueName);
        $this->assertStringContainsString($timestamp,$uniqueName);
        //this to make sure the name is unique
        $this->assertStringNotContainsString($microsecond, $uniqueName);
    }
}

