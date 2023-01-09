<?php

namespace Database\Factories;

use App\Domains\Properties\Models\PropertyUnit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class PropertyUnitFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyUnit::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "price" => $this->faker->numberBetween(5000, 10000),
        ];
    }

    public function priced($prices) {
      $sequence = array_reduce($prices, function ($priceSequence, $price) {
        $priceSequence['price'] = $price;
        return $priceSequence;
      }, []);
      return $this->state(new Sequence($sequence));
      // return $this->state(function (array $attributes) use ($sequence) {
      //   return new Sequence($sequence);
      // });
    }
}
