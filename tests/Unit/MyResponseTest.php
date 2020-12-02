<?php

namespace Tests\Unit;

use App\Libs\MyResponse;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use Tests\CreatesApplication;

class MyResponseTest extends TestCase
{
    use CreatesApplication;

    /** @test **/
    public function an_instance_can_be_made()
    {
        $myResponse = MyResponse::make();
        $this->assertEquals(new MyResponse(), $myResponse);
    }

    /** @test **/
    public function data_can_be_set()
    {
        $data = [
            'prop' => 'test',
            'prop2' => 'test2'
        ];

        $response = MyResponse::make()->data($data);

        $this->assertEquals($data, $response->json()->getData(true)['data']);
    }

    /** @test **/
    public function errors_can_be_set()
    {
        $errors = [
          'name' => 'can not be empty',
          'type' => 'should exist'
        ];

        $response = MyResponse::make()->errors($errors);

        $this->assertEquals($errors,$response->json()->getData(true)['errors']);
    }

    /** @test **/
    public function can_return_invalid_response()
    {
        $errors = [
            'name' => 'can not be empty',
            'type' => 'should exist'
        ];

        $jsonResponse = MyResponse::make()->isNotValid($errors)->json();
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(400,$jsonResponse->getStatusCode());
        $this->assertEquals($errors,$arrayResponse['errors']);

        $this->assertArrayHasKeys(['code','message','data','errors'], $arrayResponse);
    }

    /** @test **/
    public function can_return_notFound_response()
    {
        $jsonResponse = MyResponse::make()->isNotFound()->json();
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(404,$jsonResponse->getStatusCode());
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    /** @test **/
    public function can_return_isCreated_response()
    {
        $jsonResponse = MyResponse::make()->isCreated()->json();
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(201,$jsonResponse->getStatusCode());
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    /** @test **/
    public function can_return_isUpdated_response()
    {
        $jsonResponse = MyResponse::make()->isUpdated()->json();
        $arrayResponse = $jsonResponse->getData(true);

        $this->assertEquals(200,$jsonResponse->getStatusCode());
        $this->assertArrayHasKeys(['code','message','data'], $arrayResponse);
        $this->assertArrayNotHasKey('errors', $arrayResponse);
    }

    /** @test **/
    public function can_return_isDeleted_response()
    {
        $jsonResponse = MyResponse::make()->isDeleted()->json();
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
