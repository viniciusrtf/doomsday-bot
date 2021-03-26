<?php

namespace App\Notifications\NearEarthObjects;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Messages\MailMessage;

class EmailNeoNotification extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $hazardousNeos;

    /**
     * Create a new job instance.
     *
     * @param Collection $hazardousNeos  HazardousNeos
     */
    public function __construct(Collection $hazardousNeos)
    {
        $this->hazardousNeos = $hazardousNeos;
    }

        /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Asteroid alert!')
            ->view('near-earth-objects.neo-alert', ['user' => $notifiable, 'hazardousNeos' => $this->hazardousNeos]);
    }
}
