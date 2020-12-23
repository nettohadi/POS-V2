<?php

namespace Tests;

use App\Exceptions\ApiActionException;
use App\Exceptions\ApiNotFoundException;
use App\Exceptions\ApiValidationException;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    function removeColumn(array &$array, array $columns){
        foreach ($columns as $column){
            unset($array[$column]);
        }
    }
    function removeTimeStamp(array &$array){
        $this->removeColumn($array,['created_at','updated_at']);
    }
    function removeTimeStampAndImage(array &$array, String $imageColumnName='image'){
        $this->removeTimeStamp($array);
        $this->removeColumn($array, [$imageColumnName]);
    }
    function expectNotFoundException()
    {
        $this->withoutExceptionHandling();
        $this->expectException(ApiNotFoundException::class);
    }
    function expectValidationException()
    {
        $this->withoutExceptionHandling();
        $this->expectException(ApiValidationException::class);
    }
    function expectActionException()
    {
        $this->withoutExceptionHandling();
        $this->expectException(ApiActionException::class);
    }
}
