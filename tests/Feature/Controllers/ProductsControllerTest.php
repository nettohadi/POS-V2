<?php

namespace Tests\Feature\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private $tableName = 'products';
    private $fakeImageName = 'product.jpg';
    private $fakeImagePath = '/products/';

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

        $expectedProducts = [];

        //Create two Products which has the word 'Ayam'
        $expectedProducts[] = Product::factory()->create([
            'name' => 'Ayam Goreng'
        ])->toArray();
        $expectedProducts[] = Product::factory()->create([
            'name' => 'Dada Ayam'
        ])->toArray();

        //Create 10 random products
        Product::factory()->count(5)->create();

        /* 2. Invoke ------------------------------ */
        $response = $this->get(route('products.index').'?name=Ayam');

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertCount(2,$response->json('data'));
        $this->assertEquals($expectedProducts, $response->json('data'));
    }

    /** @test **/
    public function products_will_be_empty_if_names_does_not_match_the_keywords()
    {
        /* 1. Setup ------------------------------ */

        //Create two Products which has the word 'Ayam'

        Product::factory()->create([
            'name' => 'Ayam Goreng'
        ]);

        Product::factory()->create([
            'name' => 'Dada Ayam'
        ]);

        //Create 10 random products
        Product::factory()->count(5)->create();

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
        $this->expectNotFoundException();
        /* Setup */
        $product = Product::factory()->create();

        /* Invoke */
        $response = $this->get(route('products.show',['product' => 'random_id']));

        /* Assert */
        $response->assertStatus(404);
        $this->assertNull($response->json('data'));
    }

    /**
     * @test *
     * @dataProvider validProduct
     *
     */
    public function a_product_can_be_inserted($field, $value)
    {
        $this->withoutExceptionHandling();

        /* 1. Setup ------------------------------ */
        $product = $this->makeProduct();
        $product[$field] = $value;

        /* 2. Invoke ------------------------------ */
        $response = $this->post(route('products.store'),$product);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(201);

        $data = $response->json('data');

        //remove timestamp
        $this->removeTimeStamp($data);

        //remove image because we can not compare them-------
        $this->removeColumn($data,['image']);
        $this->removeColumn($product,['image']);
        //---------------------------------------------------

        $this->assertEquals($product, $data);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseHas($this->tableName,$data);
    }

    /** @test **/
    public function a_product_image_can_be_uploaded_during_insert()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        //create a fake storage
        Storage::fake('public');
        //add fake image file
        $data = $this->makeProduct();
        $data['image'] = UploadedFile::fake()->image($this->fakeImageName);

        /* 2. Invoke --------------------------------*/
        $timestamp = Carbon::now()->timestamp;
        $response = $this->post(route('products.store'),$data);

        /* 3. Assert --------------------------------*/
        $response->assertStatus(201);
        $savedProduct = $response->json('data');

        $this->assertStringContainsString("product_{$timestamp}", $savedProduct['image']);

        // Assert the file was stored
        Storage::disk('public')->assertExists($savedProduct['image']);
    }

    /**
     * @test *
     * @dataProvider invalidProduct
     */
    public function a_product_can_not_be_inserted($field, $value)
    {
        /* 1. Setup --------------------------------*/
        $product = $this->makeProduct();
        $product[$field] = $value;

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('products.store'),$product);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(400);
        $this->assertArrayHasKey('errors',$response->json());
        $this->assertArrayHasKey($field,$response->json('errors'));

        /* 3.1 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, $product);
    }

    /**
     * @test *
     * @dataProvider validProduct
     */
    public function a_product_can_be_updated($field, $value)
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $initialImage = UploadedFile::fake()->image($this->fakeImageName);
        $product = $this->getProductFromDB(['image' => $initialImage]);
        //edit data
        $product[$field] = $value;

        /* 2. Invoke --------------------------------*/
        $response = $this->put(route('products.update',['product' => $product['id']]),$product);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(200);

        $data = $response->json('data');
        $this->removeTimeStamp($data);
        $this->removeTimeStamp($product);

        //remove image because we can not compare them-------
        $this->removeColumn($data,['image']);
        $this->removeColumn($product,['image']);
        //---------------------------------------------------

        $this->assertEquals($product, $data);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseHas($this->tableName,$data);
    }

    /**
     * @test *
     * @dataProvider invalidProduct
     */
    public function a_product_can_not_be_updated($field, $value)
    {
        $this->expectValidationException();

        /* 1. Setup --------------------------------*/
        $initialImage = $this->fakeImagePath.$this->fakeImageName;
        $product = $this->getProductFromDB(['image' => $initialImage]);
        //edit data
        $product[$field] = $value;

        /* 2. Invoke --------------------------------*/
        $response = $this->put(route('products.update',['product' => $product['id']]),$product);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(400);
        $this->assertArrayHasKey('errors',$response->json());
        $this->assertArrayHasKey($field,$response->json('errors'));

        /* 3.1 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, $product);
    }

    /** @test **/
    public function a_product_image_can_be_uploaded_during_update()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        //create a fake storage
        Storage::fake('public');
        //add fake image file
        $product = $this->getProductFromDB();
        $product['image'] = UploadedFile::fake()->image($this->fakeImageName);

        /* 2. Invoke --------------------------------*/
        $timestamp = Carbon::now()->timestamp;
        $response = $this->put(route('products.update',['product' => $product['id']]),$product);

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $data = $response->json('data');

        // Assert the file was stored
        Storage::disk('public')->assertExists($data['image']);
    }

    /** @test **/
    public function a_product_image_should_not_change_if_no_image_is_uploaded_during_update()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $initialImage = $this->fakeImagePath.$this->fakeImageName;
        $product = $this->getProductFromDB(['image' => $initialImage]);

        //update image to null
        $product['image'] = null;

        /* 2. Invoke --------------------------------*/
        $response = $this->put(route('products.update',['product' => $product['id']]),$product);

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertNotNull($response->json('data')['image']);
    }

    /** @test **/
    public function a_product_can_not_be_updated_if_does_not_exist()
    {
        $this->expectNotFoundException();

        /* 1. Setup --------------------------------*/
        $product = $this->makeProduct();

        /* 2. Invoke --------------------------------*/
        $response = $this->put(route('products.update',['product' => 'invalid id']),$product);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(404);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName,$product);
    }

    /** @test **/
    public function a_product_can_be_deleted()
    {
        /* 1. Setup --------------------------------*/
        $product = Product::factory()->create()->toArray();
        $this->removeTimeStamp($product);

        //Just to make sure it exists in database
        $this->assertDatabaseHas($this->tableName,$product);

        /* 2. Invoke --------------------------------*/
        $response = $this->delete(route('products.destroy',['product' => $product['id']]));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(200);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, $product);
    }

    /** @test */
    public function a_product_can_not_be_deleted_if_does_not_exist()
    {
        $this->expectNotFoundException();
        /* 1. Setup --------------------------------*/
        $product = Product::factory()->create()->toArray();
        $this->removeTimeStamp($product);

        //Just to make sure it exists in database
        $this->assertDatabaseHas($this->tableName,$product);

        /* 2. Invoke --------------------------------*/
        // we pass  id of product we did not create
        $response = $this->delete(route('products.destroy',['product' => 'no_id']));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(404);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, ['id' => 'no_id']);
    }

    private function getProductFromDB(array $valuesToInclude=[]){
        //create Unit in DB
        $unitFromDB = Unit::factory()->create();

        //create Category in DB
        $categoryFromDB = Category::factory()->create();

        $values = array_merge([
            'unit_id' => $unitFromDB->id,
            'category_id' => $categoryFromDB->id
        ],$valuesToInclude);

        //create product in DB & return it
        return Product::factory()->create($values)->toArray();
    }
    private function makeProduct(){
        //create Unit in DB
        $unitFromDB = Unit::factory()->create();

        //create Category in DB
        $categoryFromDB = Category::factory()->create();

        $product = Product::factory()->make([
            'unit_id'     => $unitFromDB->id,
            'category_id' => $categoryFromDB->id
            ])->toArray();

        $this->removeTimeStamp($product);

        return $product;
    }
    public function invalidProduct()
    {
        return [
            'Name is required'                       => ['name',null],
            'Unit id is required'                    => ['unit_id',null],
            'Unit id must be numeric'                => ['unit_id','string'],
            'Unit id must exist in db'               => ['unit_id','random_id'],
            'Category is required'                   => ['category_id',null],
            'Category id must be numeric'            => ['category_id','string'],
            'Category id must exist in db'           => ['category_id','random_id'],
            'Stock Type is required'                 => ['stock_type',null],
            'Stock Type must be string'              => ['stock_type',1],
            'Stock Type must be single or composite' => ['stock_type','double'],
            'For sale is required'                   => ['for_sale',null],
            'For sale must be boolean'               => ['for_sale','true'],
            'Image must be image file'               => ['image','not-image']
        ];
    }
    public function validProduct()
    {
        return [
            'Barcode is not required'                 => ['barcode',null],
            'Name Initial is not required'            => ['name_initial',null],
            'Primary ingredient id is not required'   => ['primary_ingredient_id',null],
            'Primary ingredient qty is not required'  => ['primary_ingredient_qty',null],
            'Image is not required'                   => ['image',null],
            'Minimum qty is not required'             => ['minimum_qty',null],
            'Minimum expiration days is not required' => ['minimum_expiration_days',null],
        ];
    }
}
