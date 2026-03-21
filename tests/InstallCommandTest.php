<?php

use Carbon\Carbon;

it('runs the install command and exits successfully', function () {
    $this->artisan('mykad:install')
        ->assertSuccessful();
});

it('shows nesbot/carbon in the dependency table', function () {
    $this->artisan('mykad:install')
        ->expectsOutputToContain('nesbot/carbon')
        ->assertSuccessful();
});

it('shows carbon is installed when carbon is available', function () {
    // Carbon is in require-dev so it is present in the test environment
    expect(class_exists(Carbon::class))->toBeTrue();

    $this->artisan('mykad:install')
        ->expectsOutputToContain('installed')
        ->assertSuccessful();
});

it('shows what carbon enables in the table output', function () {
    $this->artisan('mykad:install')
        ->expectsOutputToContain('Carbon date objects')
        ->assertSuccessful();
});
