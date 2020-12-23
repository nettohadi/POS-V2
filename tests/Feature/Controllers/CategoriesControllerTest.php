<?php

namespace Tests\Feature\Controllers;

use App\Exceptions\ApiNotFoundException;
use App\Models\Category;
use App\Models\Product;
use App\Models\Type;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\CreatesApplication;
use Tests\TestCase;

class CategoriesControllerTest extends TestCase
{
    use  RefreshDatabase, withFaker;
    private $tableName = 'categories';

    /** @test **/
    public function categories_can_be_retrieved()
    {
        /* 1. Setup --------------------------------*/
        Type::factory()->count(6)->create();
        $categories = Category::factory()->count(7)->create()->load('type');

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('categories.index'));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response */
        $response->assertStatus(200);
        $this->assertCount(7,$response->json('data'));

        $this->assertEquals($categories->toArray(), $response->json('data'));

        /* 3.2 Database */
        $this->assertDatabaseCount($this->tableName,7);

    }

    /** @test **/
    public function categories_can_be_retrieved_with_pagination_default_perpage()
    {
        /* 1. Setup --------------------------------*/
        Type::factory()->count(6)->create();
        $categories = Category::factory()->count(20)->create()
                       ->load('type')->take(10);

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('categories.index'));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response */
        $response->assertStatus(200);
        $this->assertCount(10,$response->json('data'));
        $this->assertEquals($categories->toArray(), $response->json('data'));

        /* 3.2 Database */
        $this->assertDatabaseCount($this->tableName,20);

    }

    /** @test **/
    public function categories_can_be_retrieved_with_pagination_5_perpage()
    {
        /* 1. Setup --------------------------------*/
        Type::factory()->count(6)->create();
        $categories = Category::factory()->count(20)->create()
                      ->load('type')->take(5);

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('categories.index').'?perPage=5');

        /* 3. Assert --------------------------------*/
        /* 3.1 Response */
        $response->assertStatus(200);
        $this->assertCount(5,$response->json('data'));
        $this->assertEquals($categories->toArray(), $response->json('data'));

        /* 3.2 Database */
        $this->assertDatabaseCount($this->tableName,20);

    }

    /** @test **/
    public function a_category_can_be_found()
    {

        /* 1. Setup --------------------------------*/
        Type::factory()->count(6)->create();
        $category = Category::factory()->create()->load('type');

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('categories.show',['category' => $category->id]));

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertEquals($category->toArray(),$response->json('data'));

    }

    /** @test **/
    public function a_category_can_not_be_found()
    {
        /* 1. Setup --------------------------------*/
        Type::factory()->count(6)->create();
        Category::factory()->create()->load('type');

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('categories.show',['category' => 'random_id']));

        /* 3. Assert --------------------------------*/
        $response->assertStatus(404);

    }

    /** @test **/
    public function categories_can_be_filtered_by_name()
    {
        /* 1. Setup ------------------------------ */

        //Create two Categorys which has word 'paket'
        Category::factory()->create([
            'name' => 'Paket Hemat'
        ]);
        Category::factory()->create([
            'name' => 'Paket Geprek'
        ]);

        //Create 10 random categories
        Category::factory()->count(10)->create();

        /* 2. Invoke ------------------------------ */
        $route = route('categories.index').'?name=paket';
        $response = $this->get($route);

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertCount(2,$response->json('data'));
    }

    /** @test **/
    public function a_category_can_be_inserted()
    {
        /* 1. Setup --------------------------------*/
        $category = $this->makeCategory();

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('categories.store'),$category);

        /* 3. Assert --------------------------------*/
        /* 3.2 Response */
        $response->assertStatus(201);

        $result = $response->json();
        $data = $result['data'];
        $this->removeTimeStamp($data);

        $this->assertArrayNotHasKey('errors', $result);
        $this->assertEquals($category, $data);

        /* 3.2 Database */
        $this->assertDatabaseHas($this->tableName, $category);
        $this->assertDatabaseHas('types',['id' => $category['type_id']]);

    }

    /** @test **/
    public function name_is_required_during_insert()
    {
        /* 1. Setup --------------------------------*/
        $category = $this->makeCategory();
        $category['name'] = '';

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('categories.store'),$category);

        /* 3. Assert --------------------------------*/
        /* 3.2 Response */
        $response->assertStatus(400);

        $result = $response->json();


        $this->assertArrayHasKey('errors', $result);
        $this->assertNotNull($result['errors']);
        $this->assertArrayHasKey('name', $result['errors']);

        /* 3.2 Database */
        unset($category['type']);
        $this->assertDatabaseMissing($this->tableName, $category);
    }

    /** @test **/
    public function desc_is_not_required_during_insert()
    {
        /* 1. Setup --------------------------------*/
        $category = $this->makeCategory();
        $category['desc'] = null;

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('categories.store',$category));

        /* 3. Assert --------------------------------*/
        /* 3.2 Response */
        $response->assertStatus(201);

        $result = $response->json();


        $this->removeTimeStamp($result['data']);

        $this->assertArrayNotHasKey('errors', $result);
        $this->assertEquals($category, $result['data']);

        /* 3.2 Database */
        $this->removeColumn($category,['type']);
        $this->assertDatabaseHas($this->tableName, $category);
    }

    /** @test **/
    public function type_is_required_during_insert()
    {
        /* 1. Setup --------------------------------*/
        $category = $this->makeCategory();
        $category['type_id'] = null;

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('categories.store',$category));

        /* 3. Assert --------------------------------*/
        /* 3.2 Response */
        $response->assertStatus(400);

        $result = $response->json();
        unset($result['data']['created_at']);
        unset($result['data']['updated_at']);

        $this->assertArrayHasKey('errors', $result);
        $this->assertNotNull($result['errors']);
        $this->assertArrayHasKey('type_id', $result['errors']);

        /* 3.2 Database */
        unset($category['type']);
        $this->assertDatabaseMissing($this->tableName, $category);
    }

    /** @test **/
    public function type_must_exist_in_database_during_insert()
    {
        /* 1. Setup --------------------------------*/
        $category = $this->makeCategory();
        $category['type_id'] = 'random_id';

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('categories.store',$category));

        /* 3. Assert --------------------------------*/
        /* 3.2 Response */
        $response->assertStatus(400);

        $result = $response->json();
        unset($result['data']['created_at']);
        unset($result['data']['updated_at']);

        $this->assertArrayHasKey('errors', $result);
        $this->assertNotNull($result['errors']);
        $this->assertArrayHasKey('type_id', $result['errors']);

        /* 3.2 Database */
        unset($category['type']);
        $this->assertDatabaseMissing($this->tableName, $category);
        $this->assertDatabaseMissing('types', ['id' => $category['type_id']]);
    }

    /** @test **/
    public function a_category_can_be_updated()
    {
        $type = Type::factory()->create();
        $category = Category::create([
            'name' => 'name',
            'desc' => 'desc',
            'type_id' => $type->id
        ])->toArray();

        $category['name'] = 'New name';
        $category['desc'] = 'New desc';

        $response = $this->put(route('categories.update',
            ['category' => $category['id']]),
            $category
        );

        $response->assertStatus(200);

        $result = $response->json('data');

        $this->assertNotNull($result);
        $this->assertEquals($category, $result);

        unset($category['created_at']);
        unset($category['updated_at']);
        $this->assertDatabaseHas($this->tableName,$category);

    }

    /** @test **/
    public function a_category_can_not_be_updated_if_does_not_exist()
    {
        /** Setup */
        Type::factory()->create();
        $category = Category::factory()->create(['type_id' => 1])->toArray();
        $category['name'] = 'New name';
        $category['desc'] = 'New desc';

        /** Invoke */
        //Category with an id of 99 does not exist
        $response = $this->put(route('categories.update',
            ['category' => 99]),
            $category
        );

        $response->assertStatus(404);
        $this->assertNull($response->json('data'));
    }

    /** @test **/
    public function name_is_required_during_update()
    {
        $type = Type::factory()->create();
        $category = Category::create([
            'name' => 'name',
            'desc' => 'desc',
            'type_id' => $type->id
        ])->toArray();

        $category['name'] = '';
        $category['desc'] = 'New desc';

        $response = $this->put(route('categories.update',
            ['category' => $category['id']]),
            $category
        );

        $response->assertStatus(400);

        $result = $response->json();

        $this->assertArrayHasKey('errors',$result);
        $this->assertArrayHasKey('name', $result['errors']);

        unset($category['created_at']);
        unset($category['updated_at']);
        $this->assertDatabaseMissing($this->tableName,$category);

    }

    /** @test **/
    public function type_is_required_during_update()
    {
        $type = Type::factory()->create();
        $category = Category::create([
            'name' => 'name',
            'desc' => 'desc',
            'type_id' => $type->id
        ])->toArray();

        $category['type_id'] = null;

        $response = $this->put(route('categories.update',
            ['category' => $category['id']]),
            $category
        );

        $response->assertStatus(400);

        $result = $response->json();

        $this->assertArrayHasKey('errors',$result);
        $this->assertArrayHasKey('type_id', $result['errors']);

        unset($category['created_at']);
        unset($category['updated_at']);
        $this->assertDatabaseMissing($this->tableName,$category);

    }

    /** @test **/
    public function type_must_exist_in_database_during_update()
    {
        $type = Type::factory()->create();
        $category = Category::create([
            'name' => 'name',
            'desc' => 'desc',
            'type_id' => 10
        ])->toArray();

        $category['name'] = '';
        $category['desc'] = 'New desc';

        $response = $this->put(route('categories.update',
            ['category' => $category['id']]),
            $category
        );

        $response->assertStatus(400);

        $result = $response->json();

        $this->assertArrayHasKey('errors',$result);
        $this->assertArrayHasKey('type_id', $result['errors']);

        unset($category['created_at']);
        unset($category['updated_at']);
        $this->assertDatabaseMissing($this->tableName,$category);

    }

    /** @test **/
    public function desc_is_not_required_during_update()
    {
        $type = Type::factory()->create();
        $category = Category::create([
            'name' => 'name',
            'desc' => 'desc',
            'type_id' => $type->id
        ])->toArray();

        $category['name'] = 'New Name';
        $category['desc'] = null;

        $response = $this->put(route('categories.update',
            ['category' => $category['id']]),
            $category
        );

        $response->assertStatus(200);

        $result = $response->json('data');

        $this->assertNotNull($result);
        $this->assertEquals($category, $result);

        unset($category['created_at']);
        unset($category['updated_at']);
        $this->assertDatabaseHas($this->tableName,$category);


    }

    /** @test **/
    public function a_category_can_be_deleted()
    {
        /* Setup -----------------------------------------------*/
        $category = Category::factory()->create();

        /* Invoke -----------------------------------------------*/
        $response = $this->delete(route('categories.destroy',['category'=>$category->id]));

        /* Assert -----------------------------------------------*/
        $response->assertStatus(200);
        $this->assertDatabaseMissing($this->tableName, $category->makeHidden(['created_at','updated_at'])
            ->toArray());
    }

    /** @test **/
    public function a_category_can_not_be_deleted_if_does_not_exist()
    {
        /* Setup -----------------------------------------------*/
        $category = Category::factory()->create();

        /* Invoke -----------------------------------------------*/
        /* There is no Category with an id of 99 */
        $response = $this->delete(route('categories.destroy',['category'=> 99]));

        /* Assert -----------------------------------------------*/
        $response->assertStatus(404);
        $this->assertNull($response->json('data'));
    }

    /** @test **/
    public function a_category_can_not_be_deleted_if_has_one_or_more_products()
    {
        /* 1.Setup ----------------------------------------------------------*/
        $category = Category::factory()->create()->toArray();
        Product::factory()->count(1)->create(['category_id' => $category['id']]);

        /* 2.Invoke ----------------------------------------------------------*/
        $response = $this->delete(route('categories.destroy',['category'=> $category['id']]));

        /* 3.Assert ----------------------------------------------------------*/
        /* 3.1 Response ----------------------------------------------------------*/
        $response->assertStatus(400);

        /* 3.2 Database ----------------------------------------------------------*/
        $this->removeTimeStamp($category);
        $this->assertDatabaseHas($this->tableName, $category);

    }

    private function makeCategory(){
        $type = Type::create([
            'name' => 'Paket',
            'desc' => 'Paket Makanan'
        ]);

        $category = [
            'id' => 1,
            'name' => 'Paket',
            'desc' => 'Paket Murah Meriah',
            'type_id' => $type->id
        ];

        return $category;
    }





}
