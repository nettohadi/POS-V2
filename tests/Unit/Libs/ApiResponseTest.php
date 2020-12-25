<?php

namespace Tests\Unit\Libs;

use App\Libs\ApiResponse;
use App\Models\Dummy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\CreatesApplication;

class ApiResponseTest extends TestCase
{
    use CreatesApplication, RefreshDatabase;

    /** @test **/
    public function an_instance_can_be_made()
    {
        $myResponse = ApiResponse::make();
        $this->assertEquals(new ApiResponse(), $myResponse);
    }

    /** @test **/
    public function data_can_be_set()
    {
        $data = [
            'prop' => 'test',
            'prop2' => 'test2'
        ];

        $response = ApiResponse::make()->data($data);

        $this->assertEquals($data, $response->json()->getData(true)['data']);
    }

    /** @test **/
    public function paginator_can_be_set()
    {
        $this->withoutExceptionHandling();

        /*Setup------------------------------------------------*/
        $perPage  = 3;
        $total    = 10;
        $lastPage = (int)(ceil($total / $perPage));

        // make a paginator
        $paginator = $this->makePaginator($perPage, $lastPage, $total);

        //Create dummy data in database
        Dummy::factory()->count($total)->create();
        $dummies = Dummy::paginate($perPage);
        $dummies->setPath('http://api.dev/orders');
        /*Setup------------------------------------------------*/

        /*Invoke ------------------------------------------------*/
        $jsonResponse = ApiResponse::make()->paginator($dummies)->json();

        /*Assert*/
        $this->assertEquals($paginator,$jsonResponse->getData(true)['paginator']);

    }

    /** @test **/
    public function errors_can_be_set()
    {
        /*setup*/
        $errors = [
          'name' => 'can not be empty',
          'type' => 'should exist'
        ];

        /*Invoke*/
        $response = ApiResponse::make()->errors($errors);

        /*Assert*/
        $this->assertEquals($errors,$response->json()->getData(true)['errors']);
    }

    /** @test **/
    public function can_return_invalid_response()
    {
        /*Setup*/
        $errors = [
            'name' => 'can not be empty',
            'type' => 'should exist'
        ];

        /*Invoke*/
        $jsonResponse = ApiResponse::make()->isNotValid($errors)->json();

        /*Assert*/
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(400,$jsonResponse->getStatusCode());
        $this->assertEquals($errors,$arrayResponse['errors']);
        $this->assertArrayHasKeys(['code','message','data','errors'], $arrayResponse);
    }

    /** @test **/
    public function can_return_notFound_response()
    {
        /*Invoke*/
        $jsonResponse = ApiResponse::make()->isNotFound()->json();

        /*Assert*/
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(404,$jsonResponse->getStatusCode());
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    /** @test **/
    public function can_return_unauthenticated_response()
    {
        /*Invoke*/
        $jsonResponse = ApiResponse::make()->isNotAuthenticated('Unauthenticated')->json();

        /*Assert*/
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(401,$jsonResponse->getStatusCode());
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
        $this->assertEquals('Unauthenticated',$arrayResponse['message']);
    }

    /** @test **/
    public function can_return_notAllowed_response()
    {
        /*Invoke*/
        $jsonResponse = ApiResponse::make()->isNotAllowed('Not Allowed')->json();

        /*Assert*/
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(403,$jsonResponse->getStatusCode());
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
        $this->assertEquals('Not Allowed',$arrayResponse['message']);
    }

    /**
     * @test
     * @dataProvider validData
     */
    public function can_return_isCreated_response($data)
    {
        /*Invoke*/
        $jsonResponse = ApiResponse::make()->isCreated($data)->json();

        /*Assert*/
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(201,$jsonResponse->getStatusCode());
        $this->assertEquals($data,$arrayResponse['data']);
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    /**
     * @test *
     * @dataProvider validData
     */
    public function can_return_isUpdated_response($data)
    {
        /* Invoke */
        $jsonResponse = ApiResponse::make()->isUpdated($data)->json();

        /* Assert */
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(200,$jsonResponse->getStatusCode());
        $this->assertEquals($data, $arrayResponse['data']);
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    /**
     * @test *
     * @dataProvider validData
     */
    public function can_return_isDeleted_response($data)
    {
        /*Invoke*/
        $jsonResponse = ApiResponse::make()->isDeleted($data)->json();

        /*Assert*/
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(200,$jsonResponse->getStatusCode());
        $this->assertEquals($data, $arrayResponse['data']);
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    private function assertArrayHasKeys(array $keys, array $array){
        foreach ($keys as $item){
            $this->assertArrayHasKey($item, $array);
        }
    }

    private function assertArrayNotHasKeys(array $keys, array $array){
        foreach ($keys as $item){
            $this->assertArrayNotHasKey($item, $array);
        }
    }

    public function validData(){
        return [
            "Data can be null" => [null],
            "Data is not null" => ['prop' => 'test', 'prop2' => 'test2']
        ];
    }

    private function makePaginator($perPage, $lastPage, $total){
        return [
            'links' => [
                'first'=> 'http://api.dev/orders?page=1',
                'last' => 'http://api.dev/orders?page='.$lastPage,
                'prev' => 'http://api.dev/orders?page=1',
                'next' => 'http://api.dev/orders?page=2'
            ],
            'meta' => [
                'current_page'=> 1,
                'last_page'   => $lastPage,
                'path'        => 'http://api.dev/orders',
                'per_page'    => $perPage,
                'from'        => 1,
                'to'          => $perPage,
                'total'       => $total
            ]
        ];
    }
}
