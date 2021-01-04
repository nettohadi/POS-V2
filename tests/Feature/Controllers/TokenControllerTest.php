<?php

namespace Tests\Feature\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TokenControllerTest extends TestCase
{
    use RefreshDatabase;
    private $tokenTable = 'personal_access_tokens';

   /** @test **/
   public function a_user_can_get_token()
   {
        $this->withoutExceptionHandling();
       /* 1. Setup --------------------------------*/
       $user = User::factory()->create(['password' => Hash::make('12345678')]);

       /* 2. Invoke --------------------------------*/
       $response = $this->post(route('tokens.store'),[
           'email' => $user->email,
           'password' => '12345678',
           'device_name' => 'device x'
       ]);

       /* 3. Assert --------------------------------*/
       /* 3.1 Response --------------------------------*/
       $response->assertStatus(201);

       /* 3.1 Database --------------------------------*/
       $this->assertDatabaseHas($this->tokenTable,[
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $user->id,
            'id' => $user->tokens->first()->id
       ]);

   }

    /** @test */
    public function a_user_can_not_get_token_if_password_is_incorrect()
    {

        /* 1. Setup --------------------------------*/
        $user = User::factory()->create();

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('tokens.store'),[
            'email' => $user->email,
            'password' => 'random_password'
        ]);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(401);

        /* 3.1 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tokenTable,[
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $user->id
        ]);

    }

    /** @test */
    public function a_user_can_not_get_token_if_email_is_not_found()
    {

        /* 1. Setup --------------------------------*/
        $user = User::factory()->create(['password' => Hash::make('12345678')]);

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('tokens.store'),[
            'email' => 'random_email@email.com',
            'password' => '12345678'
        ]);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(404);

        /* 3.1 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tokenTable,[
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $user->id
        ]);

    }

    /**
     * @test *
     * @dataProvider invalidRequest
     * @param $field
     * @param $value
     */
    public function a_user_can_not_get_token_if_request_is_invalid($field, $value)
    {
        /* 1. Setup --------------------------------*/
        $user = User::factory()->create([
            'email' => 'hadi@gmail.com',
            'password' => Hash::make('12345678')
        ]);

        $request = $user->only('email','password');

        $request[$field] = $value;

        /* 2. Invoke --------------------------------*/
        $response = $this->post(route('tokens.store'),$request);

        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(400);
        $this->assertArrayHasKey('errors',$response->json());
        $this->assertArrayHasKey($field,$response->json('errors'));

        /* 3.2 Database --------------------------------*/
        $this->assertDatabaseMissing($this->tokenTable,[
            'tokenable_type' => 'App\Models\User',
            'tokenable_id' => $user->id
        ]);

    }

    /** @test **/
    public function a_user_can_remove_token()
    {

        /* 1. Setup --------------------------------*/
        $user = User::factory()->create(['password' => Hash::make('123')]);
        $tokenPlainText = $user->getToken('123');
        $token = $user->tokens->first()->only(['tokenable_id','tokenable_type','id']);
        //assign role
        $user->assignRole(tap(Role::factory()->create())
                                  ->allowTo(Permission::factory()->create(['name'=>'tokens.destroy'])));

        // assert database to make sure that token was generated
        $this->assertDatabaseHas($this->tokenTable,$token);

        /* 2. Invoke --------------------------------*/
        $response = $this->delete(route('tokens.destroy'),[],[
            'HTTP_AUTHORIZATION' => 'Bearer '.$tokenPlainText
        ]);


        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertOk();

        /* 3. Database --------------------------------*/
        $this->assertDatabaseMissing($this->tokenTable,$token);
    }

    /** @test **/
    public function a_user_can_not_remove_token_if_unauthenticated()
    {

        /* 1. Setup --------------------------------*/
        $user = User::factory()->create(['password' => Hash::make('123')]);

        /* 2. Invoke --------------------------------*/
        $response = $this->delete(route('tokens.destroy'));


        /* 3. Assert --------------------------------*/
        /* 3.1 Response --------------------------------*/
        $response->assertStatus(401);
    }

    public function invalidRequest()
    {
        return [
            'Email is required'                      => ['email',null],
            'Email must be valid email'              => ['email','random_email'],
            'Password must be at least 6 characters' => ['password','123'],
            'Password is required'                   => ['password',null],
        ];
    }
}
