<?php

namespace Database\Factories;

use App\Domains\CRM\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClientFactory extends Factory
{

  
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Client::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
          "names" => $this->faker->name(),
          "lastnames" => $this->faker->lastName(),
          "email" => $this->faker->unique()->email(),
          "cellphone" => $this->faker->unique()->phoneNumber(),
          "address_details" => $this->faker->address(),
          "dni_type" => "DNI",
          "dni" => $this->faker->unique()->phoneNumber()
        ];
    }
}
