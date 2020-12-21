<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => $this->faker->unique()->word(),
            'barcode' => $this->faker->unique()->word(),
            'name' => $this->faker->name,
            'name_initial' => null,
            'unit_id' => $this->faker->numberBetween(1,6),
            'category_id' => $this->faker->numberBetween(1,6),
            'stock_type' => $this->faker->randomElement(['single','composite']),
            'primary_ingredient_id' => $this->faker->unique()->word(),
            'primary_ingredient_qty' => $this->faker->randomNumber(),
            'for_sale' => (rand(0,1) == 1),
            'image' => null,
            'minimum_qty' => 0,
            'minimum_expiration_days' => 0,
            'updated_at' => null,
        ];
    }
}
