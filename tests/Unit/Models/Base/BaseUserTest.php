<?php

namespace Tests\Unit\Models\Base;

use App\Exceptions\ApiAuthenticationException;
use App\Exceptions\ApiNotFoundException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BaseUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test **/
    public function a_user_can_be_found_by_email()
    {
        /* 1. Setup --------------------------------*/
        User::factory()->create(['email' => 'hadi@email.com']);

        /* 2. Invoke --------------------------------*/
        $user = User::findByEmailOrThrow('hadi@email.com');

        /* 3. Assert --------------------------------*/
        $this->assertNotNull($user);
        $this->assertEquals('hadi@email.com',$user->email);
    }

    /** @test */
    public function a_user_can_not_be_found_by_email_will_throw_exception()
    {
        $this->expectException(ApiNotFoundException::class);
        $user = User::findByEmailOrThrow('random_email');
    }

    /** @test **/
    public function user_password_can_be_checked()
    {
        /* 1. Setup --------------------------------*/
        $user = User::factory()->create(['password' => Hash::make('12345678')]);

        /* 2. Invoke --------------------------------*/
        $result = $user->checkPassword('12345678');

        /* 3. Assert --------------------------------*/
        $this->assertTrue($result);
    }

    /** @test **/
    public function if_user_password_is_incorrect_will_throw_exception()
    {
        $this->expectException(ApiAuthenticationException::class);

        /* 1. Setup --------------------------------*/
        $user = User::factory()->create(['password' => Hash::make('12345678')]);

        /* 2. Invoke --------------------------------*/
        $user->checkPassword('random_password');
    }
}
