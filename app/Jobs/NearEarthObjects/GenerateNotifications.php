<?php

namespace App\Jobs\NearEarthObjects;

use App\User;
use App\Models\NearEarthObjects\NearEarthObject;
use App\Repositories\NearEarthObjects\NeoRepositoryInterface;
use App\Notifications\NearEarthObjects\EmailNeoNotification;
use App\Notifications\NearEarthObjects\TelegramNeoNotification;
use App\Formatters\NearEarthObjetcs\Neo as NeoFormatter;

use Mail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class GenerateNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $neoRepository;

    /**
     * Execute the job.
     *
     * @return void
     */ 
    public function handle()
    {
        // Get all subscribed users
        $subscribedUsers = User::where('is_neo_notification_enabled', '=', '1')->get();

        // Identify clusters of neo_notification_days_in_advance, so we don't have to execute a SELECT in near_earth_objects for each user.
        $daysInAdvanceClusters = $subscribedUsers->pluck('neo_notification_days_in_advance')->unique();
        
        // Get hazardous NeoEarthObjects for each cluster
        $neoRepository = app()->make(NeoRepositoryInterface::class);
        $from = Carbon::now();
        $hazardousNeoPerDaysInAdvance = [];
        foreach ($daysInAdvanceClusters as $daysInAdvance) {
            $until = Carbon::now()->addDays($daysInAdvance);
            $hazardousNeoPerDaysInAdvance[$daysInAdvance] = $neoRepository->getHazardous($from, $until);
        }

        foreach ($subscribedUsers as $user) {
            $hazardousNeos = $hazardousNeoPerDaysInAdvance[$user->neo_notification_days_in_advance];
            
            if (!empty($hazardousNeos) && $user->neo_notification_channel === 'telegram') {
                // Not using default Laravel's Notification in this case because I would have to add laravel-notification-channels/telegram 
                // as a new dependency, but botman/driver-telegram is already in the dependency tree and it has more features.
                TelegramNeoNotification::dispatch($user, $hazardousNeos);
            }

            if (!empty($hazardousNeos) && $user->neo_notification_channel === 'email') {
                $user->notify(new EmailNeoNotification($hazardousNeos));
            }
        }
    }
}
