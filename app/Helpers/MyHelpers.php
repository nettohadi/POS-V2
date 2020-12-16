<?php

if (function_exists('removeColumn')){
    function removeColumn(array &$array, array $columns){
        foreach ($columns as $column){
            unset($array[$column]);
        }
    }
}

if (function_exists('removeTimeStamp')){
    function removeTimeStamp(array &$array){
        removeColumn($array,['created_at','updated_at']);
    }
}
