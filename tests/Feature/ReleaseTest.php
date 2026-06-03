<?php

use App\Models\Admin;
use App\Models\Release;

beforeEach(function () {
    $this->admin = Admin::factory()->create(['status' => 'active', 'role' => 'super']);
});

test('latestPublished retorna a release publicada mais recente', function () {
    Release::create(['title' => 'A', 'released_at' => '2026-06-01', 'content' => 'x', 'published' => true]);
    $r2 = Release::create(['title' => 'B', 'released_at' => '2026-06-02', 'content' => 'y', 'published' => true]);

    expect(Release::latestPublished()->id)->toBe($r2->id);
});

test('latestPublished ignora releases nao publicadas', function () {
    $published = Release::create(['title' => 'Pub', 'released_at' => '2026-06-01', 'content' => 'x', 'published' => true]);
    Release::create(['title' => 'Draft', 'released_at' => '2026-06-02', 'content' => 'y', 'published' => false]);

    expect(Release::latestPublished()->id)->toBe($published->id);
});

test('latestPublished retorna null se nao houver release publicada', function () {
    Release::create(['title' => 'Draft', 'released_at' => '2026-06-01', 'content' => 'x', 'published' => false]);

    expect(Release::latestPublished())->toBeNull();
});

test('popup aparece no dashboard apos login', function () {
    Release::create(['title' => 'Teste', 'released_at' => '2026-06-03', 'content' => 'conteudo de teste', 'published' => true]);

    $response = $this->actingAs($this->admin, 'admin')
        ->get('/admin');

    $response->assertOk();
    $response->assertSee('release-popup-overlay', false);
    $response->assertSee('Teste');
});

test('popup nao aparece numa segunda visita na mesma sessao', function () {
    Release::create(['title' => 'Teste', 'released_at' => '2026-06-03', 'content' => 'conteudo', 'published' => true]);

    $this->actingAs($this->admin, 'admin');

    $first = $this->get('/admin');
    $first->assertSee('release-popup-overlay', false);

    $second = $this->get('/admin');
    $second->assertDontSee('release-popup-overlay', false);
});
