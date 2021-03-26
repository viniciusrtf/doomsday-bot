<?php

namespace App\Conversations\NearEarthObjects;

use App\User;
use Carbon\Carbon;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class HowFarInAdvanceYouWishToBeNotified extends Conversation
{
    protected $bot;
    protected $telegram;
    protected $user;

    private function howFarInAdvanceYouWishToBeNotified()
    {
        $question = Question::create('How far in advance you wish to be alerted of a dangerous asteroid?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('1 day')->value(1),
                Button::create('3 days')->value(3),
                Button::create('1 week')->value(7),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->user->update(['neo_notification_days_in_advance' => $answer->getValue()]);
                $this->say('Ok, I can do this! Not that I have a choice, but I digress...');
                $this->say("Whenever there's a chance for your existence to be terminated by an asteroid in {$answer->getValue()} day(s), I'll let you know (too).");
            } else {
                $this->say('Sorry, I didn\'t get it.. Tell the dev to use Watson or something for NLP next time, would ya?');
                $this->howFarInAdvanceYouWishToBeNotified();
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->bot       = $this->getBot();
        $this->telegram  = $this->bot->getUser();

        $this->user = User::firstOrCreate(
            ['telegram_id' => $this->telegram->getId()],
            [
                'name'     => $this->telegram->getFirstName() . ' ' . $this->telegram->getLastName(),
                'email'    => $this->telegram->getId() . '@undefined.com',
                'info'     => json_encode($this->telegram->getInfo()),
                'password' => bcrypt($this->telegram->getId()),
            ]
        );

        $this->howFarInAdvanceYouWishToBeNotified();
    }
}