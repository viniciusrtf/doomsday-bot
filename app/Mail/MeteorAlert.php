<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MeteorAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $alerts;

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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Alerta de meteoro!')->view('meteor-alert');
    }
}
