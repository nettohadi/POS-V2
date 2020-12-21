<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersControllerTest extends TestCase
{
    use RefreshDatabase;

   /** @test **/
   public function users_can_be_retrieved()
   {
       $this->withoutExceptionHandling();

       /* 1. Setup --------------------------------*/
       $users = User::factory()->count(10)->create();

       /* 2. Invoke --------------------------------*/
       $response = $this->get(route('users.index'));

       /* 3. Assert --------------------------------*/
       $response->assertStatus(200);
       $this->assertEquals($users->toArray(), $response->json('data'));
   }

   /** @test **/
   public function users_can_be_retrieved_with_pagination()
   {
       /* 1. Setup --------------------------------*/
       $users = User::factory()->count(10)->create()->take(5)->toArray();

       /* 2. Invoke --------------------------------*/
       $response = $this->get(route('users.index') . '?perPage=5');

       /* 3. Assert --------------------------------*/
       $response->assertStatus(200);
       $this->assertEquals($users, $response->json('data'));
   }

   /** @test **/
   public function users_can_be_filtered_by_name()
   {
       /* 1. Setup --------------------------------*/

       //create 2 users with names contain 'abdul'
       $expectedUsers = [];
       $expectedUsers[] = User::factory()->create(['name' => 'Abdul Hadi'])->toArray();
       $expectedUsers[] = User::factory()->create(['name' => 'Abdul Muid'])->toArray();

       //create 5 random users
       User::factory()->count(5)->create();

       /* 2. Invoke --------------------------------*/
       $response = $this->get(route('users.index').'?name=abdul');

       /* 3. Assert --------------------------------*/
       $response->assertStatus(200);
       $this->assertEquals($expectedUsers, $response->json('data'));
   }

    /** @test **/
    public function users_will_be_empty_if_names_does_not_match_the_keywords()
    {
        /* 1. Setup ------------------------------ */

        //Create two users which has the word 'abdul'

        User::factory()->create([
            'name' => 'Abdul Hadi'
        ]);

        User::factory()->create([
            'name' => 'Abdul Muid'
        ]);

        //Create 10 random users
        User::factory()->count(5)->create();

        /* 2. Invoke ------------------------------ */
        $response = $this->get(route('users.index').'?name=random_name');

        /* 3. Assert --------------------------------*/
        $response->assertStatus(200);
        $this->assertCount(0,$response->json('data'));
        $this->assertEquals([], $response->json('data'));
    }

    /** @test **/
    public function a_user_can_be_found()
    {
        $this->withoutExceptionHandling();

        /*Setup ---------------------------------*/
        $user = User::factory()->create();

        /*Invoke ---------------------------------*/
        $response = $this->get(route('users.show',['user' => $user->id]));

        /*Assert ---------------------------------*/
        $response->assertStatus(200);
        $this->assertEquals($user->toArray(),$response->json('data'));

    }

    /** @test **/
    public function a_user_can_not_be_found()
    {
        $this->expectNotFoundException();

        /* Setup */
        $user = User::factory()->create();

        /* Invoke */
        $response = $this->get(route('users.show',['user' => 'random_id']));

        /* Assert */
        $response->assertStatus(404);
        $this->assertNull($response->json('data'));
    }

    private function getUserFromDB(array $values=[]){
        //create user in DB & return it
        return User::factory()->create($values)->toArray();
    }

    public function invalidUser()
    {
        return [
            'Name is required'                        => ['name',null],
            'Sex  is required'                        => ['sex',null],
            'Email is required'                       => ['email',null],
            'Email must be valid'                     => ['email','email'],
            'Password must be at least 6 characters'  => ['password','123'],
            'role is required'                        => ['role',null],
            'Image must be image file'                => ['image','not-image']
        ];
    }

    public function validUser()
    {
        return [
            'Barcode is not required'                 => ['barcode',null],
            'Name Initial is not required'            => ['name_initial',null],
            'Primary ingredient id is not required'   => ['primary_ingredient_id',null],
            'Primary ingredient qty is not required'  => ['primary_ingredient_qty',null],
            'Image is not required'                   => ['image',null],
            'Minimum qty is not required'             => ['minimum_qty',null],
            'Minimum expiration days is not required' => ['minimum_expiration_days',null],
        ];
    }
}
