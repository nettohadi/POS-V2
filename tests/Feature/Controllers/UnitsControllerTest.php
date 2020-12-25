<?php

namespace Tests\Feature\Controllers;

use App\Models\Product;
use App\Models\Unit;
use Database\Factories\UnitFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UnitsControllerTest extends TestCase
{
    use RefreshDatabase, withFaker;
    private $tableName = 'units';

    function __construct()
    {
        parent::__construct();
    }

    /** @test **/
    public function units_can_be_retrieved()
    {
        $units = Unit::factory()->count(7)->create();

        $response = $this->get(route('units.index'));

        $response->assertStatus(200);
        $this->assertCount(7,$response->json('data'));
        $this->assertEquals($units->toArray(), $response->json('data'));

        $this->assertDatabaseCount($this->tableName,7);
        $this->assertDatabaseHas($this->tableName,
            $units->makeHidden(['created_at','updated_at'])
            ->last()->toArray());

    }

    /** @test **/
    public function units_can_be_retrieved_with_pagination_default_perpage()
    {
        /* 1. Setup --------------------------------*/
        $units = Unit::factory()->count(20)->create();

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('units.index'));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response */
        $response->assertStatus(200);
        $this->assertCount(10,$response->json('data'));
        $this->assertEquals($units->take(10)->toArray(), $response->json('data'));

        /* 3.2 Database */
        $this->assertDatabaseCount($this->tableName,20);
        $this->assertDatabaseHas($this->tableName,
            $units->makeHidden(['created_at','updated_at'])
                ->last()->toArray());

    }

    /** @test **/
    public function units_can_be_retrieved_with_pagination_5_perpage()
    {
        /* 1. Setup --------------------------------*/
        $units = Unit::factory()->count(20)->create();

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('units.index').'?perPage=5');

        /* 3. Assert --------------------------------*/
        /* 3.1 Response */
        $response->assertStatus(200);
        $this->assertCount(5,$response->json('data'));
        $this->assertEquals($units->take(5)->toArray(), $response->json('data'));

        /* 3.2 Database */
        $this->assertDatabaseCount($this->tableName,20);
        $this->assertDatabaseHas($this->tableName,
            $units->makeHidden(['created_at','updated_at'])
                ->last()->toArray());

    }

    /** @test **/
    public function units_can_be_filtered_by_name()
    {
        /* 1. Setup ------------------------------ */

        //Create two Units which has word 'liter'
        Unit::factory()->create([
            'name' => 'Liter'
        ]);
        Unit::factory()->create([
            'name' => 'Mililiter'
        ]);

        //Create 10 random units
        Unit::factory()->count(10)->create();

        /* 2. Invoke ------------------------------ */
        $route = route('units.index').'?name=liter';
        $response = $this->get($route);

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertCount(2,$response->json('data'));
    }

    /** @test **/
    public function a_unit_can_be_found()
    {
        $unit = Unit::factory()->create();

        $response = $this->get(route('units.show',['unit' => $unit->id]));

        $response->assertStatus(200);
        $this->assertEquals($unit->toArray(),$response->json('data'));

    }

    /** @test **/
    public function a_unit_can_not_be_found()
    {

        /* Setup */
        $unit = Unit::factory()->create();

        /* Invoke */
        $response = $this->get(route('units.show',['unit' => 100]));

        /* Assert */
        $response->assertStatus(404);
        $this->assertNull($response->json('data'));

    }

    /** @test **/
    public function a_unit_can_be_inserted()
    {

        $unit['name'] = $this->faker->name;
        $unit['desc'] = $this->faker->sentence(3);

        $response = $this->post(route('units.store'),$unit);

        $response->assertStatus(201);
        $this->assertDatabaseHas($this->tableName,$unit);
    }

    /** @test **/
    public function name_is_required_during_insert()
    {

        /* Setup */
        $unit['name'] = null;
        $unit['desc'] = $this->faker->sentence(3);

        /* Invoke */
        $response = $this->post(route('units.store'),$unit);

        /* Assert */
        $response->assertStatus(400);
        $this->assertDatabaseMissing($this->tableName, $unit);
    }

    /** @test **/
    public function desc_is_not_required_during_insert(){

        /* Setup */
        $unit['name'] = $this->faker->name;
        $unit['desc'] = null;

        /* Invoke */
        $response = $this->post(route('units.store'),$unit);

        /* Setup */
        $response->assertStatus(201);
        $this->assertDatabaseHas($this->tableName, $unit);
    }

    /** @test **/
    public function a_unit_can_be_updated()
    {

        $unit['id'] = Unit::factory()->create()->id;
        $unit['name'] = 'New name';
        $unit['desc'] = 'New desc';

        $response = $this->put(route('units.update',
            ['unit' => $unit['id']]),
            $unit
        );


        $response->assertStatus(200);
        $this->assertNotNull($response->json('data'));
        $this->assertEquals($unit['name'], $response->json('data')['name']);
        $this->assertEquals($unit['desc'], $response->json('data')['desc']);

        $this->assertDatabaseHas($this->tableName,$unit);

    }

    /** @test **/
    public function a_unit_can_not_be_updated_if_does_not_exist()
    {

        /* Setup */
        $unit['id'] = Unit::factory()->create()->id;
        $unit['name'] = 'New name';
        $unit['desc'] = 'New desc';

        /* Invoke */
        $response = $this->put(route('units.update',
            ['unit' => 99]),
            $unit
        );


        /* Assert */
        $response->assertStatus(404);
        $this->assertNull($response->json('data'));
    }

    /** @test **/
    public function name_is_required_during_update(){

        /* Setup */
        $unit['id'] = Unit::factory()->create()->id;
        $unit['name'] = null;
        $unit['desc'] = 'New desc';

        /* Invoke */
        $response = $this->put(route('units.update',
            ['unit' => $unit['id']]),
            $unit
        );

        /* Assert */
        $response->assertStatus(400);
        $this->assertNotNull($response->json('errors')['name']);
        $this->assertDatabaseMissing($this->tableName, $unit);
    }

    /** @test **/
    public function desc_is_not_required_during_update(){


        $unit['id'] = Unit::factory()->create()->id;
        $unit['name'] = 'New name';
        $unit['desc'] = null;

        $response = $this->put(route('units.update',
            ['unit' => $unit['id']]),
            $unit
        );

        $response->assertStatus(200);
        $this->assertEquals($unit['name'],$response->json('data')['name']);
        $this->assertEquals($unit['desc'],$response->json('data')['desc']);

        $this->assertDatabaseHas('units',$unit);
    }

    /** @test **/
    public function a_unit_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $unit = Unit::factory()->create();

        $response = $this->delete(route('units.destroy',['unit'=>$unit->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing($this->tableName, $unit->makeHidden(['created_at','updated_at'])
             ->toArray());
    }

    /** @test **/
    public function a_unit_can_not_be_deleted_if_does_not_exist()
    {
        /* Setup */
        $unit = Unit::factory()->create();

        /* Invoke */
        $response = $this->delete(route('units.destroy',['unit'=> 99]));

        /* Assert */
        $response->assertStatus(404);
    }

    /** @test **/
    public function a_unit_can_not_be_deleted_if_has_one_or_more_products()
    {
        /* 1.Setup ----------------------------------------------------------*/
        $unit = Unit::factory()->create()->toArray();
        Product::factory()->count(1)->create(['unit_id' => $unit['id']]);

        /* 2.Invoke ----------------------------------------------------------*/
        $response = $this->delete(route('units.destroy',['unit'=> $unit['id']]));

        /* 3.Assert ----------------------------------------------------------*/
        /* 3.1 Response ----------------------------------------------------------*/
        $response->assertStatus(403);

        /* 3.2 Database ----------------------------------------------------------*/
        $this->removeTimeStamp($unit);
        $this->assertDatabaseHas($this->tableName, $unit);
        /* 3.3 Exception ---------------------------------------------------------*/

    }
}
