<?php

use App\Models\Admin;
use App\Models\Collaborator;
use App\Models\Company;
use App\Models\Leader;
use App\Models\MagicLink;
use App\Models\Scenario;

beforeEach(function () {
    $admin = Admin::factory()->create(['status' => 'active']);

    $this->company = Company::factory()->create([
        'created_by_admin_id' => $admin->id,
        'license' => 'demo',
        'status' => 'active',
    ]);

    $this->leader = Leader::factory()->create([
        'company_id' => $this->company->id,
        'status' => 'active',
    ]);

    $this->collaborator = Collaborator::factory()->create([
        'company_id' => $this->company->id,
        'invited_by_leader_id' => $this->leader->id,
    ]);

    // Create 3 demo-eligible scenarios
    Scenario::factory()->count(3)->create([
        'company_id'    => null,
        'is_default'    => true,
        'demo_eligible' => true,
        'status'        => 'active',
    ]);
});

test('unauthenticated collaborator is redirected from training', function () {
    $this->get(route('training.index'))->assertRedirect(route('magic-link.invalid'));
});

test('authenticated collaborator can access training index', function () {
    $this->actingAs($this->collaborator, 'collaborator');

    $this->withSession(['training.welcome_seen' => true])
        ->get(route('training.index'))
        ->assertOk();
});

test('training index shows scenarios for demo company', function () {
    $this->actingAs($this->collaborator, 'collaborator');

    $response = $this->withSession(['training.welcome_seen' => true])
        ->get(route('training.index'));

    $response->assertOk();
    $response->assertViewHas('scenarios');
});

test('completing training marks collaborator as done', function () {
    $this->actingAs($this->collaborator, 'collaborator')
        ->withSession(['training.welcome_seen' => true]);

    $scenarios = Scenario::where('demo_eligible', true)->get();

    $this->withSession(['training.welcome_seen' => true])
        ->get(route('training.index'));

    foreach ($scenarios as $i => $scenario) {
        $questions = collect($scenario->content['messages'])->where('type', 'question');
        foreach ($questions as $question) {
            $correctOption = collect($question['options'])->firstWhere('correct', true);
            $this->post(route('training.answer'), [
                'scenario_id'       => $scenario->id,
                'question_index'    => 0,
                'chosen_option_key' => $correctOption['key'],
                'response_time_ms'  => 1500,
            ]);
        }
    }

    $this->collaborator->refresh();
    expect($this->collaborator->completed_at)->not->toBeNull();
});
