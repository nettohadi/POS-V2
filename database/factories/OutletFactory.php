<?php

namespace Database\Factories;

use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

class OutletFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Outlet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => (string)$this->faker->lexify('???'),
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'vat_percentage' => $this->faker->numberBetween(1,50),
            'dp_percentage' => $this->faker->numberBetween(1,50),
            'social_media' => $this->faker->word(),
            'contact' => $this->faker->word()
        ];
    }
}
