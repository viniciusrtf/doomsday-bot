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
        \Log::debug(__METHOD__);
        $botman = app('botman');

        if (!empty($this->alerts)) {
            $botman->say('Olá! Estes são os meteoros que oferecem algum perigo à Terra nos próximos ' . $this->user->doomsday_advance . ' dia(s).', $this->user->telegram_id, TelegramDriver::class);
            foreach ($this->alerts as $alert) {
                \Log::debug($alert);
                \Log::debug(
                    'Nome: '            . $alert->name          . '\n' .
                    'Data da Aprox: '   . $alert->approach_date . '\n' .
                    'Diâmetro est.: '   . number_format($alert->estimated_diameter, 2, ',', '.') . ' Km'   . '\n' .
                    'Velocidade rel.: ' . number_format($alert->relative_velocity, 2, ',', '.')  . ' Km/h' . '\n' .
                    'Distância (min): ' . number_format($alert->mass_distance, 2, ',', '.')      . ' Km'   . '\n' .
                    'Mais detalhes: '   . $alert->nasa_url
                );
                sleep(2);
                $botman->say(urlencode(
                    'Nome: '            . $alert->name          . '\n' .
                    'Data da Aprox: '   . $alert->approach_date . '\n' .
                    'Diâmetro est.: '   . number_format($alert->estimated_diameter, 2, ',', '.') . ' Km'   . '\n' .
                    'Velocidade rel.: ' . number_format($alert->relative_velocity, 2, ',', '.')  . ' Km/h' . '\n' .
                    'Distância (min): ' . number_format($alert->mass_distance, 2, ',', '.')      . ' Km'   . '\n' .
                    'Mais detalhes: '   . $alert->nasa_url
                ), $this->user->telegram_id, TelegramDriver::class);
            }
        }
    }
}
