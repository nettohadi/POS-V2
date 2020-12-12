<?php

namespace Tests\Feature;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $tableName = 'products';

    /** @test **/
    public function products_can_be_retrieved()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $products = Product::factory()->count(3)->create();

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('products.index'));

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertEquals($products->toArray(), $response->json('data'));
    }

    /** @test **/
    public function products_can_be_retrieved_using_pagination()
    {
        /* 1. Setup --------------------------------*/
        $products = Product::factory()->count(10)->create();

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('products.index').'?perPage=5');

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertEquals($products->take(5)->toArray(), $response->json('data'));
        $this->assertDatabaseCount('products',10);
    }

    /** @test **/
    public function products_can_be_filtered_by_name()
    {
        /* 1. Setup ------------------------------ */

        //Create two Products which has the word 'Ayam'
        $expectedProducts = [];

        $expectedProducts[] = Product::factory()->create([
            'name' => 'Ayam Goreng'
        ])->toArray();

        $expectedProducts[] = Product::factory()->create([
            'name' => 'Dada Ayam'
        ])->toArray();

        //Create 10 random products
        Product::factory()->count(10)->create();

        /* 2. Invoke ------------------------------ */
        $response = $this->get(route('products.index').'?name=Ayam');

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertCount(2,$response->json('data'));
        $this->assertEquals($expectedProducts, $response->json('data'));
    }

    /** @test **/
    public function products_will_be_empty_if_names_does_not_contain_the_keywords()
    {
        /* 1. Setup ------------------------------ */

        //Create two Products which has the word 'Ayam'
        $expectedProducts = [];

        Product::factory()->create([
            'name' => 'Ayam Goreng'
        ]);

        Product::factory()->create([
            'name' => 'Dada Ayam'
        ]);

        //Create 10 random products
        Product::factory()->count(10)->create();

        /* 2. Invoke ------------------------------ */
        $response = $this->get(route('products.index').'?name=Kayam');

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertCount(0,$response->json('data'));
        $this->assertEquals([], $response->json('data'));
    }

    /** @test **/
    public function a_product_can_be_found()
    {
        $this->withoutExceptionHandling();

        /*Setup ---------------------------------*/
        $product = Product::factory()->create();

        /*Invoke ---------------------------------*/
        $response = $this->get(route('products.show',['product' => $product->id]));

        /*Assert ---------------------------------*/
        $response->assertStatus(200);
        $this->assertEquals($product->toArray(),$response->json('data'));

    }

    /** @test **/
    public function a_product_can_not_be_found()
    {
        $this->withoutExceptionHandling();
        $product = Product::factory()->create();

        $response = $this->get(route('products.show',['product' => 100]));

        $response->assertStatus(404);
        $this->assertNull($response->json('data'));

    }

    /** @test **/
    public function a_product_can_be_inserted()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup ------------------------------ */
        $product = $this->getProduct();

        /* 2. Invoke ------------------------------ */
        $response = $this->post(route('products.store'),$product);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(201);
        $data = $this->removeTimestamp($response->json('data'));
        $this->assertEquals($product, $data);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseHas($this->tableName,$data);
    }

    /** @test **/
    public function name_is_required_during_insert()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $product = $this->getProduct();
        /*set name to null*/
        $product['name'] = null;

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('products.store'),$product);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(400);
        $this->assertArrayHasKey('errors',$response->json());
        $this->assertArrayHasKey('name',$response->json('errors'));

        /* 3.1 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, $product);

    }

    /** @test **/
    public function barcode_is_not_required_during_insert()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $product = $this->getProduct();
        /*set barcode to null*/
        $product['barcode'] = null;



        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('products.store'),$product);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(201);

        $this->assertArrayNotHasKey('errors',$response->json());

        /* 3.1 Database --------------------------------*/
        $this->assertDatabaseHas($this->tableName, $product);

    }

    private function getProduct(){
        return [
            'id' => $this->faker->unique()->word(),
            'barcode' => $this->faker->unique()->word(),
            'name' => $this->faker->name,
            'name_initial' => 'AT',
            'unit_id' => $this->faker->numberBetween(1,6),
            'category_id' => $this->faker->numberBetween(1,6),
            'stock_type' => $this->faker->randomElement(['single','composite']),
            'primary_ingredient_id' => $this->faker->unique()->word(),
            'primary_ingredient_qty' => $this->faker->randomNumber(),
            'for_sale' => (rand(0,1) == 1),
            'image' => null,
            'minimum_qty' => 0,
            'minimum_expiration_days' => 0
        ];
    }
    private function removeTimestamp(array $array){
        unset($array['created_at']);
        unset($array['updated_at']);

        return $array;
    }
}
