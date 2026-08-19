<?php

use App\Models\Admin;
use App\Models\Collaborator;
use App\Models\Company;
use App\Models\Leader;
use App\Models\PlatformFeedback;
use App\Models\Scenario;

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
});

test('block_feedback e injetado quando termina TODOS os cenarios de uma plataforma e proxima e diferente', function () {
    PlatformFeedback::updateOrCreate(
        ['platform' => 'wapp'],
        [
            'title'  => 'Aprendizado WhatsApp!',
            'slides' => [
                ['title' => 'Slide 1', 'body' => '<p>Cuidado com golpes.</p>'],
                ['title' => 'Slide 2', 'body' => '<p>Sempre valide.</p>'],
            ],
        ]
    );

    $wappScenario = Scenario::factory()->create([
        'platform' => 'wapp',
        'is_default' => true,
        'demo_eligible' => true,
        'status' => 'active',
    ]);
    Scenario::factory()->create([
        'platform' => 'teams',
        'is_default' => true,
        'demo_eligible' => true,
        'status' => 'active',
    ]);
    Scenario::factory()->create([
        'platform' => 'email',
        'is_default' => true,
        'demo_eligible' => true,
        'status' => 'active',
    ]);

    $this->actingAs($this->collaborator, 'collaborator');
    $this->withSession(['training.welcome_seen' => true])->get(route('training.index'));

    $question = collect($wappScenario->content['messages'])->firstWhere('type', 'question');
    $chosen = collect($question['options'])->firstWhere('correct', true);

    $response = $this->postJson(route('training.answer'), [
        'scenario_id'       => $wappScenario->id,
        'question_index'    => 0,
        'chosen_option_key' => $chosen['key'],
        'response_time_ms'  => 1500,
    ]);

    $response->assertOk();
    $response->assertJson([
        'scenario_complete' => true,
        'training_complete' => false,
        'quick_transition'  => false,
        'block_feedback' => [
            'platform' => 'wapp',
            'title'    => 'Aprendizado WhatsApp!',
            'slides'   => [
                ['title' => 'Slide 1', 'body' => '<p>Cuidado com golpes.</p>'],
                ['title' => 'Slide 2', 'body' => '<p>Sempre valide.</p>'],
            ],
        ],
    ]);
});

test('block_feedback NAO e injetado quando proximo cenario e da mesma plataforma', function () {
    PlatformFeedback::updateOrCreate(
        ['platform' => 'wapp'],
        ['title' => 'X', 'slides' => [['title' => null, 'body' => '<p>Y</p>']]]
    );

    $wapp1 = Scenario::factory()->create([
        'platform' => 'wapp',
        'is_default' => true,
        'demo_eligible' => true,
        'status' => 'active',
    ]);
    Scenario::factory()->create([
        'platform' => 'wapp',
        'is_default' => true,
        'demo_eligible' => true,
        'status' => 'active',
    ]);
    Scenario::factory()->create([
        'platform' => 'teams',
        'is_default' => true,
        'demo_eligible' => true,
        'status' => 'active',
    ]);

    $this->actingAs($this->collaborator, 'collaborator');
    $this->withSession(['training.welcome_seen' => true])->get(route('training.index'));

    $q = collect($wapp1->content['messages'])->firstWhere('type', 'question');
    $chosen = collect($q['options'])->firstWhere('correct', true);

    $response = $this->postJson(route('training.answer'), [
        'scenario_id'       => $wapp1->id,
        'question_index'    => 0,
        'chosen_option_key' => $chosen['key'],
        'response_time_ms'  => 1000,
    ]);

    $response->assertOk();
    $response->assertJson([
        'scenario_complete' => true,
        'quick_transition'  => true,
        'block_feedback'    => null,
    ]);
});

test('block_feedback e injetado no ULTIMO cenario (training complete)', function () {
    PlatformFeedback::updateOrCreate(
        ['platform' => 'email'],
        [
            'title'  => 'Fim de linha E-mail',
            'slides' => [['title' => 'Fim', 'body' => '<p>Fim do treino.</p>']],
        ]
    );

    Scenario::factory()->create([
        'platform' => 'wapp',
        'is_default' => true,
        'demo_eligible' => true,
        'status' => 'active',
    ]);
    Scenario::factory()->create([
        'platform' => 'teams',
        'is_default' => true,
        'demo_eligible' => true,
        'status' => 'active',
    ]);
    $emailLast = Scenario::factory()->create([
        'platform' => 'email',
        'is_default' => true,
        'demo_eligible' => true,
        'status' => 'active',
    ]);

    $this->actingAs($this->collaborator, 'collaborator');
    $this->withSession(['training.welcome_seen' => true])->get(route('training.index'));

    foreach ([Scenario::where('platform','wapp')->first(), Scenario::where('platform','teams')->first(), $emailLast] as $s) {
        $q = collect($s->content['messages'])->firstWhere('type','question');
        $chosen = collect($q['options'])->firstWhere('correct', true);
        $response = $this->postJson(route('training.answer'), [
            'scenario_id' => $s->id,
            'question_index' => 0,
            'chosen_option_key' => $chosen['key'],
            'response_time_ms' => 1000,
        ]);
    }

    $response->assertJson([
        'training_complete' => true,
        'block_feedback' => [
            'platform' => 'email',
            'title'    => 'Fim de linha E-mail',
            'slides'   => [['title' => 'Fim', 'body' => '<p>Fim do treino.</p>']],
        ],
    ]);
});

test('backward-compat: registro sem slides usa body como slide unico', function () {
    // Registro legado (pre-migration de slides) — so tem body, sem slides
    $fb = PlatformFeedback::updateOrCreate(
        ['platform' => 'wapp'],
        ['title' => 'Legado', 'body' => 'texto antigo', 'slides' => null]
    );

    expect($fb->normalized_slides)->toBe([
        ['title' => null, 'body' => 'texto antigo'],
    ]);
});
