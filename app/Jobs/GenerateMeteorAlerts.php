<?php

namespace App\Jobs;

use Queue;
use App\User;
use App\Meteor;
use App\Jobs\SendEmailAlert;
use App\Jobs\SendTelegramAlert;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateMeteorAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Get all hazardous meteors for the next 7 days
        $meteors = Meteor::whereHazardous(true)->get();

        // Get all users that subscribed to receive meteor alerts
        $users = User::whereNotNull('doomsday_alert')->get();

        foreach ($users as $user) {
            $alerts = $meteors->filter(function($meteor) use ($user) {
                return Carbon::now()->lt($meteor->approach_date) &&
                       Carbon::now()->diffInDays($meteor->approach_date) < $user->doomsday_advance;
            });

            if (count($alerts) > 1) {
                if ($user->doomsday_alert === 'telegram') {
                    \Log::debug($user);
                    \Log::debug($alerts);
                    SendTelegramAlert::dispatch(serialize($user), serialize($alerts));
                } elseif ($user->doomsday_alert === 'email') {
                    SendEmailAlert::dispatch(serialize($user), serialize($alerts));
                }
            }
        }


    }
}
