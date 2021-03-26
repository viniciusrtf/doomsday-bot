<?php

namespace App\Notifications\NearEarthObjects;

use BotMan\BotMan\BotMan;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use App\Formatters\NearEarthObjects\Neo as NeoFormatter;

class TelegramNeoNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, NeoFormatter;

    protected $user;
    protected $hazardousNeos;

    /**
     * Create a new job instance.
     *
     * @param string $user   Serialied User instance
     * @param string $hazardousNeos Serialized array with all hazardous near earth objects
     */
    public function __construct(User $user, Collection $hazardousNeos)
    {
        $this->user   = $user;
        $this->hazardousNeos = $hazardousNeos;
    }


    /**
     * Send a meteor alert via Telegram
     */
    public function handle()
    {
        $botman = app('botman');

        if (!empty($this->hazardousNeos)) {
            $botman->say('As promised, I\'m here to bring you some existential threats.', $this->user->telegram_id, TelegramDriver::class);
            $botman->say('See below which asteroids have a chance of hitting the Earth in the next ' . $this->user->neo_notification_days_in_advance . ' day(s).', $this->user->telegram_id, TelegramDriver::class);
            foreach ($this->hazardousNeos as $neo) {
                $botman->say($this::format($neo), $this->user->telegram_id, TelegramDriver::class);
            }
            $botman->say('Good luck!', $this->user->telegram_id, TelegramDriver::class);
        }
    }
}
