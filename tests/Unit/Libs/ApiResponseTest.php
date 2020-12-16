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

        $perPage  = 10;
        $total    = 30;
        $lastPage = (int)(ceil($total / $perPage));

        Dummy::factory()->count($total)->create();

        $paginator = [
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
                'to'          => 10,
                'total'       => $total
            ]
        ];
        $dummies = Dummy::paginate(10);
        $dummies->setPath('http://api.dev/orders');

        $jsonResponse = ApiResponse::make()->paginator($dummies)->json();

        $this->assertEquals($paginator,$jsonResponse->getData(true)['paginator']);

    }

    /** @test **/
    public function errors_can_be_set()
    {
        $errors = [
          'name' => 'can not be empty',
          'type' => 'should exist'
        ];

        $response = ApiResponse::make()->errors($errors);

        $this->assertEquals($errors,$response->json()->getData(true)['errors']);
    }

    /** @test **/
    public function can_return_invalid_response()
    {
        $errors = [
            'name' => 'can not be empty',
            'type' => 'should exist'
        ];

        $jsonResponse = ApiResponse::make()->isNotValid($errors)->json();
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(400,$jsonResponse->getStatusCode());
        $this->assertEquals($errors,$arrayResponse['errors']);

        $this->assertArrayHasKeys(['code','message','data','errors'], $arrayResponse);
    }

    /** @test **/
    public function can_return_notFound_response()
    {
        $jsonResponse = ApiResponse::make()->isNotFound()->json();
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(404,$jsonResponse->getStatusCode());
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    /** @test **/
    public function can_return_isCreated_response()
    {
        $jsonResponse = ApiResponse::make()->isCreated()->json();
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(201,$jsonResponse->getStatusCode());
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    /** @test **/
    public function can_return_isUpdated_response()
    {
        $jsonResponse = ApiResponse::make()->isUpdated()->json();
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(200,$jsonResponse->getStatusCode());
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    /** @test **/
    public function can_return_isDeleted_response()
    {
        $jsonResponse = ApiResponse::make()->isDeleted()->json();
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(200,$jsonResponse->getStatusCode());
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
}
