<?php

use App\Models\Admin;
use App\Models\Collaborator;
use App\Models\Company;
use App\Models\Leader;
use App\Models\MagicLink;

beforeEach(function () {
    $this->admin = Admin::factory()->create(['status' => 'active', 'role' => 'super']);

    $this->company = Company::factory()->create([
        'created_by_admin_id' => $this->admin->id,
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
});

test('valid magic link authenticates leader and redirects to dashboard', function () {
    ['plain_token' => $token] = MagicLink::generateFor($this->leader, 'leader_login', expiresDays: 7);

    $response = $this->get('/auth/acesso?t=' . $token);

    $response->assertRedirect(route('leader.dashboard'));
    $this->assertAuthenticatedAs($this->leader, 'leader');
});

test('valid magic link authenticates collaborator and redirects to training', function () {
    ['plain_token' => $token] = MagicLink::generateFor($this->collaborator, 'collaborator_training', expiresDays: 30);

    $response = $this->get('/auth/acesso?t=' . $token);

    $response->assertRedirect(route('training.index'));
    $this->assertAuthenticatedAs($this->collaborator, 'collaborator');
});

test('magic link cannot be used twice', function () {
    ['plain_token' => $token] = MagicLink::generateFor($this->collaborator, 'collaborator_training', expiresDays: 30);

    $this->get('/auth/acesso?t=' . $token);
    $secondResponse = $this->get('/auth/acesso?t=' . $token);

    $secondResponse->assertRedirect(route('magic-link.invalid'));
});

test('expired magic link is rejected', function () {
    $magicLink = MagicLink::generateFor($this->collaborator, 'collaborator_training', expiresDays: 1);
    $ml = MagicLink::where('token_hash', hash('sha256', $magicLink['plain_token']))->first();
    $ml->update(['expires_at' => now()->subDay()]);

    $response = $this->get('/auth/acesso?t=' . $magicLink['plain_token']);

    $response->assertRedirect(route('magic-link.invalid'));
});

test('missing token redirects to invalid page', function () {
    $this->get('/auth/acesso')->assertRedirect(route('magic-link.invalid'));
});

test('completely invalid token redirects to invalid page', function () {
    $this->get('/auth/acesso?t=totally-fake-token')->assertRedirect(route('magic-link.invalid'));
});
