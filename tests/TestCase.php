<?php

namespace Tests;

use App\Exceptions\ApiActionException;
use App\Exceptions\ApiNotFoundException;
use App\Exceptions\ApiValidationException;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
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

    function withAuthorization(){
        $this->withAuth();
    }

    function withAuthorizationExcept(Array $permissionsToExclude){
        $this->withAuth($permissionsToExclude);
    }

    private function withAuth(Array $permissionsToExclude=[]){
        $user = User::factory()->create();
        $user->assignRole($this->createBasicRole($permissionsToExclude));
        $this->actingAs($user);
    }

    function createBasicRole(Array $permissionsToExclude=[]){
        $permissions = collect($this->getRoutes())
                                    ->map(function ($item){return $item[1];})
                                    ->diff($permissionsToExclude);

        $role = Role::factory()->create();
        foreach ($permissions as $permission){
            $role->allowTo(Permission::factory()->create(['name' => $permission]));
        }

        return $role;
    }

    function getRoutes(){
        $routeName = $this->routeName ?? '';
        $paramName = $this->paramName ?? '';

        return [
            "{$routeName}.index" => ['get',"{$routeName}.index",[]],
            "{$routeName}.show" => ['get',"{$routeName}.show",[$paramName => 1]],
            "{$routeName}.store" => ['post',"{$routeName}.store",[]],
            "{$routeName}.update" => ['put',"{$routeName}.update",[$paramName => 1]],
            "{$routeName}.delete" => ['delete',"{$routeName}.destroy", [$paramName => 1]]
        ];
    }

}
