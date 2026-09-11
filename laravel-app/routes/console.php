<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('app:invite {--hours=72 : Number of hours before the invitation expires}', function () {
    $hours = max(1, (int) $this->option('hours'));
    $url = URL::temporarySignedRoute(
        'invite.accept',
        now()->addHours($hours),
        ['token' => Str::random(40)]
    );

    $this->line('Invitation link (expires in '.$hours.' hours):');
    $this->line($url);
})->purpose('Generate a time-limited invitation link for the application');
