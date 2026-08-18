<?php

use App\Models\Admin;
use App\Models\Answer;
use App\Models\Collaborator;
use App\Models\Company;
use App\Models\Leader;
use App\Models\Scenario;
use App\Models\TrainingSession;

beforeEach(function () {
    $admin = Admin::factory()->create(['status' => 'active']);
    $this->company = Company::factory()->create([
        'created_by_admin_id' => $admin->id,
        'license' => 'demo',
        'status'  => 'active',
    ]);
    $this->leader = Leader::factory()->create([
        'company_id' => $this->company->id,
        'status'     => 'active',
    ]);
    $this->collaborator = Collaborator::factory()->create([
        'company_id'           => $this->company->id,
        'invited_by_leader_id' => $this->leader->id,
    ]);
    $this->scenarios = Scenario::factory()->count(3)->create([
        'is_default'    => true,
        'demo_eligible' => true,
        'status'        => 'active',
    ]);
});

test('retry after failing lands on index with a new mission (nao "treinamento concluido")', function () {
    // Reproduz o bug de prod: session antiga com `started_at` em UTC (3h a frente
    // da nova session em BRT). Antes do fix, latestOfMany('started_at') retornava
    // a session antiga → completedScenarioIds contava os answers da antiga → tela
    // "Treinamento concluido" em vez da nova missao.
    $oldSession = TrainingSession::create([
        'collaborator_id'  => $this->collaborator->id,
        'started_at'       => now()->addHours(3),  // simula timezone drift (UTC vs BRT)
        'completed_at'     => now(),
        'total_scenarios'  => 3,
        'total_questions'  => $this->scenarios->sum(fn($s) => collect($s->content['messages'])->where('type','question')->count()),
        'score'            => 1,
        'passed'           => false,
        'duration_seconds' => 600,
    ]);

    // Cria Answer pra CADA pergunta de CADA cenario da sessao antiga
    // (simula o Lucas em prod que respondeu tudo mas ficou <80%)
    foreach ($this->scenarios as $scenario) {
        $questions = collect($scenario->content['messages'])->where('type', 'question')->values();
        foreach ($questions as $idx => $q) {
            $chosen = collect($q['options'])->first();
            Answer::create([
                'training_session_id' => $oldSession->id,
                'collaborator_id'     => $this->collaborator->id,
                'scenario_id'         => $scenario->id,
                'scenario_version'    => $scenario->version,
                'question_index'      => $idx,
                'chosen_option_key'   => $chosen['key'],
                'is_correct'          => (bool) ($chosen['correct'] ?? false),
                'response_time_ms'    => 1000,
                'answered_at'         => now(),
            ]);
        }
    }

    $this->collaborator->update([
        'completed_at'    => now(),
        'score'           => 1,
        'total_questions' => $oldSession->total_questions,
    ]);

    $this->actingAs($this->collaborator, 'collaborator');

    // POST /treinamento/refazer
    $retry = $this->post(route('training.retry'));
    $retry->assertRedirect(route('training.index'));

    $this->collaborator->refresh();
    expect($this->collaborator->completed_at)->toBeNull();
    expect($this->collaborator->trainingSessions()->count())->toBe(2);

    // GET /treinamento/ deveria mostrar uma missao pra fazer
    $index = $this->withSession(['training.welcome_seen' => true])->get(route('training.index'));
    $index->assertOk();
    $index->assertViewIs('training.index');

    // Assert critico: $nextScenario !== null (senao mostra "Treinamento concluido")
    $index->assertViewHas('nextScenario', function ($nextScenario) {
        return $nextScenario !== null;
    });
});
