<?php

namespace Database\Factories;

use App\Models\Scenario;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScenarioFactory extends Factory
{
    protected $model = Scenario::class;

    public function definition(): array
    {
        return [
            'company_id'    => null,
            'platform'      => fake()->randomElement(['wapp', 'teams', 'email']),
            'slug'          => fake()->unique()->slug(3),
            'label'         => fake()->sentence(3),
            'avatar'        => '🎭',
            'bg_color'      => '#1e3a8a',
            'preview'       => fake()->sentence(),
            'is_default'    => true,
            'demo_eligible' => false,
            'version'       => 1,
            'status'        => 'active',
            'content'       => [
                'messages' => [
                    ['type' => 'text', 'from' => 'them', 'body' => 'Mensagem de teste.'],
                    [
                        'type'    => 'question',
                        'prompt'  => 'Pergunta de teste?',
                        'options' => [
                            ['key' => 'a', 'text' => 'Resposta correta', 'correct' => true,  'feedback' => 'Correto!'],
                            ['key' => 'b', 'text' => 'Resposta errada',  'correct' => false, 'feedback' => 'Errado.'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
