<?php

use App\Models\Admin;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;

test('failed login increments admin failed_attempts', function () {
    $admin = Admin::factory()->create(['status' => 'active', 'failed_attempts' => 0]);

    event(new Failed('admin', $admin, ['email' => $admin->email, 'password' => 'wrong']));

    $admin->refresh();
    expect($admin->failed_attempts)->toBe(1);
});

test('admin is locked after 5 failed attempts', function () {
    $admin = Admin::factory()->create(['status' => 'active', 'failed_attempts' => 4]);

    event(new Failed('admin', $admin, ['email' => $admin->email, 'password' => 'wrong']));

    $admin->refresh();
    expect($admin->failed_attempts)->toBe(5);
    expect($admin->locked_until)->not->toBeNull();
    expect($admin->isLocked())->toBeTrue();
});

test('locked admin cannot access panel', function () {
    $admin = Admin::factory()->create([
        'status'       => 'active',
        'locked_until' => now()->addMinutes(10),
    ]);

    expect($admin->canAccessPanel(app(\Filament\Panel::class)))->toBeFalse();
});

test('suspended admin cannot access panel', function () {
    $admin = Admin::factory()->create(['status' => 'suspended']);

    expect($admin->canAccessPanel(app(\Filament\Panel::class)))->toBeFalse();
});
