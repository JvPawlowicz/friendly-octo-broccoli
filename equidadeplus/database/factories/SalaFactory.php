<?php

namespace Database\Factories;

use App\Models\Sala;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories.Factory<\App\Models\Sala>
 */
class SalaFactory extends Factory
{
    protected $model = Sala::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => 'Sala ' . $this->faker->randomElement(['Terapia', 'Fisioterapia', 'Consulta']) . ' ' . $this->faker->numberBetween(1, 5),
            'capacidade' => $this->faker->numberBetween(1, 4),
        ];
    }
}


