<?php

namespace Database\Factories;

use App\Models\UCode;
use Illuminate\Database\Eloquent\Factories\Factory;

class UCodeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UCode::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'str' => $this->faker->word(),
            'last_order' => $this->faker->randomNumber()
        ];
    }
}
