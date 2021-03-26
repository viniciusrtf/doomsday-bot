<?php

use App\Http\Controllers\DoomsdayBotController;

$botman = resolve('botman');

$botman->hears(
    'Are there any asteroid endangering Earth today\?',
    DoomsdayBotController::class.'@areThereNeoToday'
);

$botman->fallback(function($bot) {
    $bot->startConversation(app()->make(App\Conversations\NearEarthObjects\NoRandomChattingPlease::class));
});