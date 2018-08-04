<?php
use App\Http\Controllers\BotManController;
use App\Http\Controllers\DoomsdayBotController;

$botman = resolve('botman');

$botman->hears(
    'Me avise quando houver risco de colisão de um meteoro com o planeta Terra',
    DoomsdayBotController::class.'@doomsdayAlert'
);
