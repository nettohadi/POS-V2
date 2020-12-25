<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'sex' => Arr::random([1,0]),
            'email' => $this->faker->unique()->safeEmail,
            'role' => $this->faker->numberBetween(1,3),
            'shift_id' => $this->faker->numberBetween(1,3),
            'outlet_id' => $this->faker->lexify('???'),
            'image' => null,
            'password' => Hash::make('12345678')
        ];
    }
}
