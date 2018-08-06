<?php

namespace App\Jobs;

use App\User;

use BotMan\BotMan\BotMan;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendTelegramAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $alerts;


    /**
     * Create a new job instance.
     *
     * @param string $user   Serialied User instance
     * @param string $alerts Serialized array with all alerts
     */
    public function __construct(string $user, string $alerts)
    {
        $this->user   = unserialize($user);
        $this->alerts = unserialize($alerts);
    }


    /**
     * Send a meteor alert via Telegram
     */
    public function handle()
    {
        $botman = app('botman');
        $botman->say('Gotcha', $this->user->telegram_id, TelegramDriver::class);
    }
}
