<?php

use App\Models\User;

it('shows profile preview page with Edit button and informative sections', function () {
    $user = User::factory()->create([
        'nomor_induk' => 'NIS-001',
        'address' => 'Jl. Mawar No. 1',
        'jk' => 'L',
    ]);

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk();
    $response->assertSee($user->name);
    $response->assertSee($user->email);
    $response->assertSee('Edit Profile');

    // Sections (achievements removed)
    $response->assertSee('Quick Stats');
    $response->assertSee('Personal Info');
    $response->assertSee('Recent Activity');

    // Personal info fields
    $response->assertSee('ID Number');
    $response->assertSee('Gender');
    $response->assertSee('Address');
});

it('shows profile edit page with form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/profile/edit');

    $response->assertOk();
    $response->assertSee('Profile Information');
});
