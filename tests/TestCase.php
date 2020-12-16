<?php

namespace Tests;

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
}
