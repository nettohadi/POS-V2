<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;
    private $tokenTable = 'personal_access_tokens';

   /** @test **/
   public function a_user_can_login()
   {

       /* 1. Setup --------------------------------*/
       $user = User::factory()->create(['password' => Hash::make('12345678')]);

       /* 2. Invoke --------------------------------*/
       $response = $this->post(route('login.store'),[
           'email' => $user->email,
           'password' => '12345678',
           'device_name' => 'device x'
       ]);

       /* 3. Assert --------------------------------*/
       /* 3.1 Response --------------------------------*/
       $response->assertOk();

       /* 3.1 Database --------------------------------*/
       $this->assertDatabaseHas($this->tokenTable,[
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $user->id,
            'name' => 'device x'
       ]);

   }

    /**
     * @test *
     * @dataProvider invalidRequest
     */
    public function a_user_can_not_login($field, $value)
    {

        /* 1. Setup --------------------------------*/
        $user = User::factory()->create();
        $request = $user->only(['email','password']);



        $request[$field] = $value;

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('login.store'),$request);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(401);

        /* 3.1 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tokenTable,[
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $user->id
        ]);

    }

    //test user's token is expired

    //test user's token is revoked

    public function invalidRequest()
    {
        return [
            'Password is not correct'    => ['password','random_password']
        ];
    }
}
