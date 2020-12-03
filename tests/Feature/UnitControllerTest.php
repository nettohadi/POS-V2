<?php

namespace Tests\Feature;

use App\Models\Unit;
use Database\Factories\UnitFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UnitControllerTest extends TestCase
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
    public function units_can_be_retrieved_with_pagination()
    {
        $units = Unit::factory()->count(20)->create();

        $response = $this->get(route('units.index'));

        $response->assertStatus(200);

        $this->assertCount(10,$response->json('data'));
        $this->assertEquals($units->take(10)->toArray(),
            $response->json('data'));

        $this->assertDatabaseCount($this->tableName,20);
        $this->assertDatabaseHas($this->tableName,
            $units->makeHidden(['created_at','updated_at'])
                ->last()->toArray());

    }

    /** @test **/
    public function a_unit_can_be_found()
    {
        $this->withoutExceptionHandling();
        $unit = Unit::factory()->create();

        $response = $this->get(route('units.show',['unit' => $unit->id]));

        $response->assertStatus(200);
        $this->assertEquals($unit->toArray(),$response->json('data'));

    }

    /** @test **/
    public function a_unit_can_not_be_found()
    {
        $this->withoutExceptionHandling();
        $unit = Unit::factory()->create();

        $response = $this->get(route('units.show',['unit' => 100]));

        $response->assertStatus(404);
        $this->assertNull($response->json('data'));

    }

    /** @test **/
    public function a_unit_can_be_inserted()
    {
        $this->withoutExceptionHandling();

        $unit['name'] = $this->faker->name;
        $unit['desc'] = $this->faker->sentence(3);

        $response = $this->post(route('units.store'),$unit);

        $response->assertStatus(201);
        $this->assertDatabaseHas($this->tableName,$unit);
    }

    /** @test **/
    public function name_is_required_during_insert(){

        $unit['name'] = null;
        $unit['desc'] = $this->faker->sentence(3);

        $response = $this->post(route('units.store'),$unit);

        $response->assertStatus(400);
        $this->assertDatabaseMissing($this->tableName, $unit);
    }

    /** @test **/
    public function desc_is_not_required_during_insert(){

        $unit['name'] = $this->faker->name;
        $unit['desc'] = null;

        $response = $this->post(route('units.store'),$unit);

        $response->assertStatus(201);
        $this->assertDatabaseHas($this->tableName, $unit);
    }

    /** @test **/
    public function a_unit_can_be_updated()
    {
        $this->withoutExceptionHandling();

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
        $this->withoutExceptionHandling();

        $unit['id'] = Unit::factory()->create()->id;
        $unit['name'] = 'New name';
        $unit['desc'] = 'New desc';

        $response = $this->put(route('units.update',
            ['unit' => 99]),
            $unit
        );


        $response->assertStatus(404);
        $this->assertNull($response->json('data'));
    }

    /** @test **/
    public function name_is_required_during_update(){

        $this->withoutExceptionHandling();

        $unit['id'] = Unit::factory()->create()->id;
        $unit['name'] = null;
        $unit['desc'] = 'New desc';

        $response = $this->put(route('units.update',
            ['unit' => $unit['id']]),
            $unit
        );

        $response->assertStatus(400);
        $this->assertNotNull($response->json('errors')['name']);
        $this->assertDatabaseMissing($this->tableName, $unit);
    }

    /** @test **/
    public function desc_is_not_required_during_update(){

        $this->withoutExceptionHandling();

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

        $this->assertDatabaseHas($this->tableName, $unit->makeHidden(['created_at','updated_at'])
            ->toArray());

        $response = $this->delete(route('units.destroy',['unit'=>$unit->id]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing($this->tableName, $unit->makeHidden(['created_at','updated_at'])
             ->toArray());
    }

    /** @test **/
    public function a_unit_can_not_be_deleted_if_does_not_exist()
    {
        $this->withoutExceptionHandling();

        $unit = Unit::factory()->create();

        $response = $this->delete(route('units.destroy',['unit'=> 99]));

        $response->assertStatus(404);
    }
}
