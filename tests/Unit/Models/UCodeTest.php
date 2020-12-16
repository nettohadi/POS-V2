<?php

namespace Tests\Unit\Models;

use App\Models\UCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UCodeTest extends TestCase
{
    use RefreshDatabase;

    /** @test **/
    public function it_return_unique_id_based_on_non_existing_string_with_the_order_number_is_one()
    {
        /** It shoud return a unique string code with 1 as the  order number
         *  when the string given as parameter does not exist in DB
         */

        /* 1. Setup --------------------------------*/
        //create new code with string "HUM-"
        UCode::factory()->create([
            'str'        => 'HUM-',
            'last_order' => 5
        ]);

        /* 2. Invoke --------------------------------*/
        //invoke with a non existing string
        $ucode = new UCode();
        $newCode = $ucode->generate('HUMA-');

        /* 3. Assert --------------------------------*/
        $this->assertEquals('HUMA-1',$newCode);
        $this->assertDatabaseHas('ucodes',['str'=>'HUMA-','last_order' => 1]);
    }

    /** @test **/
    public function it_return_unique_id_based_on_existing_string_with_the_right_order_number()
    {
        /** It shoud return a unique string code with the right order number
         *  when the string given as parameter already exist in DB
         */

        /* 1. Setup --------------------------------*/
        UCode::factory()->create([
            'str'        => 'HUM-',
            'last_order' => 5
        ]);

        /* 2. Invoke --------------------------------*/
        $ucode = new UCode();
        $newCode = $ucode->generate('HUM-');

        /* 3. Assert --------------------------------*/
        $this->assertEquals('HUM-6',$newCode);
        $this->assertDatabaseHas('ucodes',['str'=>'HUM-','last_order' => 6]);
    }
}
