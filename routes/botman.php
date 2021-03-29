<?php

use App\Http\Controllers\DoomsdayBotController;

$botman = resolve('botman');

$botman->hears('Are there any asteroid endangering Earth today\?', function($bot) {
    $bot->startConversation(app()->make('App\Conversations\NearEarthObjects\AreThereNeoToday'));
});

$botman->fallback(function($bot) {
    $bot->startConversation(app()->make('App\Conversations\NearEarthObjects\NoRandomChattingPlease'));
});