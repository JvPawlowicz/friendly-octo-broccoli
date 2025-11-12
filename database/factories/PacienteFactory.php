<?php

namespace Database\Factories;

use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    protected $model = Paciente::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome_completo' => $this->faker->name(),
            'nome_social' => $this->faker->optional()->firstName(),
            'data_nascimento' => $this->faker->dateTimeBetween('-40 years', '-5 years'),
            'cpf' => $this->faker->unique()->numerify('###########'),
            'email_principal' => $this->faker->unique()->safeEmail(),
            'telefone_principal' => $this->faker->phoneNumber(),
            'status' => 'Ativo',
            'numero_carteirinha' => $this->faker->optional()->numerify('########'),
            'tipo_plano' => $this->faker->optional()->randomElement(['Individual', 'Familiar']),
            'diagnostico_condicao' => $this->faker->optional()->sentence(),
            'plano_de_crise' => $this->faker->optional()->sentence(),
            'alergias_medicacoes' => $this->faker->optional()->words(3, true),
            'metodo_comunicacao' => $this->faker->optional()->randomElement(['Telefone', 'WhatsApp', 'Email']),
            'informacoes_escola' => $this->faker->optional()->paragraph(),
            'informacoes_medicas_adicionais' => $this->faker->optional()->paragraph(),
            'cep' => $this->faker->postcode(),
            'logradouro' => $this->faker->streetName(),
            'numero' => $this->faker->buildingNumber(),
            'bairro' => $this->faker->citySuffix(),
            'cidade' => $this->faker->city(),
            'estado' => $this->faker->stateAbbr(),
        ];
    }
}


