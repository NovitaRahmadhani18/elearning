<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('does not include @livewireScripts when using Vite ESM', function () {
    // Render a route that uses the app layout and ensure scripts are not duplicated.
    // We will hit the login page which uses the guest layout as well.
    $response = $this->get('/login');
    $response->assertOk();

    // Ensure no classic Livewire script tag is present (cdn or blade directive output)
    $response->assertDontSee('livewire.min.js');
    $response->assertDontSee('@livewireScripts');
});
