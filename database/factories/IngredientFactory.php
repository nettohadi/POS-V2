<?php

namespace Database\Factories;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Factories\Factory;

class IngredientFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ingredient::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'recipe_id' => $this->faker->unique()->randomNumber(),
            'product_id' => $this->faker->unique()->lexify('???'),
            'qty'       => $this->faker->numberBetween(1,10),
            'main_ingredient' => $this->faker->randomKey([1, 0])
        ];
    }
}
