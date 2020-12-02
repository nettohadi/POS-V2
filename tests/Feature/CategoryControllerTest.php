<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase, withFaker;
    private $tableName = 'categories';

    function __construct()
    {
        parent::__construct();
    }

    /** @test **/
    public function categories_can_be_retrieved()
    {
        $this->withoutExceptionHandling();
        $categories = Category::factory()->count(7)->create();

        $response = $this->get(route('categories.index'));

        $response->assertStatus(200);
        $this->assertCount(7,$response->json('data'));
        $this->assertEquals($categories->toArray(), $response->json('data'));

        $this->assertDatabaseCount($this->tableName,7);
        $this->assertDatabaseHas($this->tableName,
                                $categories->makeHidden(['created_at','updated_at'])
                                ->last()->toArray());

    }

    /** @test **/
    public function category_can_be_found()
    {
        $this->withoutExceptionHandling();
        $category = Category::factory()->create();

        $response = $this->get(route('categories.show',['category' => $category->id]));

        $response->assertStatus(200);
        $this->assertEquals($category->toArray(),$response->json('data'));

    }
}
