<?php

namespace Tests\Feature\Controllers;

use App\Models\Outlet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OutletsControllerTest extends TestCase
{
    use  RefreshDatabase, withFaker;
    private $tableName = 'outlets';

    /** @test **/
    public function outlets_can_be_retrieved()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $outlets = Outlet::factory()->count(7)->create();

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('outlets.index'));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response */
        $response->assertStatus(200);
        $this->assertCount(7,$response->json('data'));

        $this->assertEquals($outlets->toArray(), $response->json('data'));

        /* 3.2 Database */
        $this->assertDatabaseCount($this->tableName,7);

    }

    /** @test **/
    public function outlets_can_be_retrieved_using_pagination()
    {
        /* 1. Setup --------------------------------*/
        $outlets = Outlet::factory()->count(10)->create();

        /* 2. Invoke --------------------------------*/
        $response = $this->get(route('outlets.index').'?perPage=5');

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertEquals($outlets->take(5)->toArray(), $response->json('data'));
        $this->assertDatabaseCount('outlets',10);
    }

    /** @test **/
    public function outlets_can_be_filtered_by_name()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup ------------------------------ */

        $expectedOUtlets = [];

        //Create two Outlets which has the word 'Jogja'
        $expectedOUtlets[] = Outlet::factory()->create([
            'name' => 'Outlet Jogja Utara'
        ])->toArray();
        $expectedOUtlets[] = Outlet::factory()->create([
            'name' => 'Outlet Jogja Barat'
        ])->toArray();

        //Create 10 random outlets
        Outlet::factory()->count(5)->create();

        /* 2. Invoke ------------------------------ */
        $response = $this->get(route('outlets.index').'?name=jogja');

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertCount(2,$response->json('data'));
        $this->assertEquals($expectedOUtlets, $response->json('data'));
    }

    /** @test **/
    public function outlets_will_be_empty_if_names_does_not_match_the_keywords()
    {
        /* 1. Setup ------------------------------ */

        //Create two Outlets which has the word 'Ayam'

        Outlet::factory()->create([
            'name' => 'Outlet Jogja Barat'
        ]);

        Outlet::factory()->create([
            'name' => 'Outlet Jogja Utara'
        ]);

        //Create 10 random outlets
        Outlet::factory()->count(5)->create();

        /* 2. Invoke ------------------------------ */
        $response = $this->get(route('outlets.index').'?name=Kayam');

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertCount(0,$response->json('data'));
        $this->assertEquals([], $response->json('data'));
    }

    /** @test **/
    public function an_outlet_can_be_found()
    {
        $this->withoutExceptionHandling();

        /*Setup ---------------------------------*/
        $outlet = Outlet::factory()->create();

        /*Invoke ---------------------------------*/
        $response = $this->get(route('outlets.show',['outlet' => $outlet->id]));

        /*Assert ---------------------------------*/
        $response->assertStatus(200);
        $this->assertEquals($outlet->toArray(),$response->json('data'));

    }

    /** @test **/
    public function an_outlet_can_not_be_found()
    {
        $this->withoutExceptionHandling();

        /*Setup ---------------------------------*/
        $outlet = Outlet::factory()->create();

        /*Invoke ---------------------------------*/
        $response = $this->get(route('outlets.show',['outlet' => 'random_id']));

        /*Assert ---------------------------------*/
        $response->assertStatus(404);
        $this->assertNull($response->json('data'));

    }

    /**
     * @test *
     * @dataProvider validOutlet
     *
     */
    public function an_outlet_can_be_inserted($field, $value)
    {
        $this->withoutExceptionHandling();

        /* 1. Setup ------------------------------ */
        $outlet = $this->makeOutlet();
        $outlet[$field] = $value;
        //add dummy column
        $outlet['dummy'] = 'dummy';

        /* 2. Invoke ------------------------------ */
        $response = $this->post(route('outlets.store'),$outlet);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(201);

        $data = $response->json('data');

        //remove timestamp
        $this->removeTimeStamp($data);
        //remove dummy column
        $this->removeColumn($outlet,['dummy']);

        $this->assertEquals($outlet, $data);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseHas($this->tableName,$data);
    }

    /**
     * @test *
     * @dataProvider invalidOutlet
     *
     */
    public function an_outlet_can_not_be_inserted($field, $value)
    {
        $this->withoutExceptionHandling();

        /* 1. Setup ------------------------------ */
        $outlet = $this->makeOutlet();
        $outlet[$field] = $value;

        /* 2. Invoke ------------------------------ */
        $response = $this->post(route('outlets.store'),$outlet);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(400);

        $response->json('data');

        //check for error messages
        $this->assertArrayHasKey('errors',$response->json());
        $this->assertArrayHasKey($field,$response->json('errors'));

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName,$outlet);
    }

    /**
     * @test *
     * @dataProvider validOutlet
     *
     */
    public function an_outlet_can_be_updated($field, $value)
    {
        $this->withoutExceptionHandling();

        /* 1. Setup ------------------------------ */
        $outlet = $this->getOutletFromDB();
        //edit outlet
        $outlet[$field] = $value;
        //add dummy column, to make sure invalid column not included
        $outlet['dummy'] = 'dummy';

        /* 2. Invoke ------------------------------ */
        $response = $this->put(route('outlets.update',['outlet'=>$outlet['id']]),
                    $outlet);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(200);

        $data = $response->json('data');

        //remove timestamp
        $this->removeTimeStamp($data);
        $this->removeTimeStamp($outlet);

        //remove dummy column, before assert
        $this->removeColumn($outlet,['dummy']);

        $this->assertEquals($outlet, $data);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseHas($this->tableName,$data);
    }

    /**
     * @test *
     * @dataProvider inValidOutlet
     *
     */
    public function an_outlet_can_not_be_updated($field, $value)
    {
        $this->withoutExceptionHandling();

        /* 1. Setup ------------------------------ */
        $outlet = $this->getOutletFromDB();
        //reserve id for update
        $id = $outlet['id'];
        //edit outlet
        $outlet[$field] = $value;

        /* 2. Invoke ------------------------------ */
        $response = $this->put(route('outlets.update',['outlet'=>$id]),
            $outlet);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(400);

        $this->assertArrayHasKey('errors',$response->json());
        $this->assertArrayHasKey($field,$response->json('errors'));

        /* 3.1 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, $outlet);
    }

    /** @test **/
    public function a_outlet_can_not_be_updated_if_does_not_exist()
    {
        $this->withoutExceptionHandling();

        /* 1. Setup --------------------------------*/
        $outlet = $this->makeOutlet();

        /* 2. Invoke --------------------------------*/
        $response = $this->put(route('outlets.update',
            ['outlet' => 'invalid id']),
            $outlet);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(404);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName,$outlet);
    }

    /** @test **/
    public function a_outlet_can_be_deleted()
    {
        /* 1. Setup --------------------------------*/
        $outlet = Outlet::factory()->create()->toArray();
        $this->removeTimeStamp($outlet);

        //Just to make sure it exists in database
        $this->assertDatabaseHas($this->tableName,$outlet);

        /* 2. Invoke --------------------------------*/
        $response = $this->delete(route('outlets.destroy',['outlet' => $outlet['id']]));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(200);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, $outlet);
    }

    /** @test */
    public function a_outlet_can_not_be_deleted_if_does_not_exist()
    {
        /* 1. Setup --------------------------------*/
        $outlet = Outlet::factory()->create()->toArray();
        $this->removeTimeStamp($outlet);

        //Just to make sure it exists in database
        $this->assertDatabaseHas($this->tableName,$outlet);

        /* 2. Invoke --------------------------------*/
        // we pass  id of outlet we did not create
        $response = $this->delete(route('outlets.destroy',['outlet' => 'no_id']));

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(404);

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tableName, ['id' => 'no_id']);
    }

    private function getOutletFromDB(array $valuesToInclude=[]){
        $values = array_merge($this->makeOutlet(),$valuesToInclude);

        //create outlet in DB & return it
        return Outlet::factory()->create($values)->toArray();
    }
    private function makeOutlet(){
        return [
            'id' => strtoupper($this->faker->lexify('???')),
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'vat_percentage' => $this->faker->numberBetween(1,50),
            'dp_percentage' => $this->faker->numberBetween(1,50),
            'social_media' => $this->faker->word(),
            'contact' => $this->faker->word()
        ];
    }
    public function validOutlet()
    {
        return [
            'vat_percentage is not required' => ['vat_percentage',null],
            'dp_percentage is not required'  => ['dp_percentage',null],
            'social media is not required'   => ['social_media',null],
            'contact is not required'        => ['contact',null],
        ];
    }
    public function inValidOutlet()
    {
        return [
            'id is required'                               => ['id',null],
            'id must be string'                            => ['id',1],
            'id must be letter'                            => ['id','1'],
            'id should only contains letter'               => ['id','A-'],
            'id should not be more than 5 letters'         => ['id','abcdefg'],
            'name is required'                             => ['name',null],
            'name must be string'                          => ['name',1],
            'address is required'                          => ['address',null],
            'address must be string'                       => ['address',1],
            'vat_percentage must be number'                => ['vat_percentage','one'],
            'vat_percentage min:0 max:100'                 => ['vat_percentage',101],
            'vat_percentage min:0 max:100'                 => ['vat_percentage',-1],
            'dp_percentage must be number'                 => ['dp_percentage','one'],
            'dp_percentage min:0 max:100'                  => ['dp_percentage',101],
            'dp_percentage min:0 max:100'                  => ['dp_percentage',-1],
            'social media must be string'                  => ['social_media',1],
            'contact must be string'                       => ['contact',1],
        ];
    }

}
