<?php

namespace App\Conversations\NearEarthObjects;

use App\User;
use Carbon\Carbon;
use Validator;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

use App\Conversations\NearEarthObjects\HowFarInAdvanceYouWishToBeNotified;

class TypeYourEmail extends Conversation
{
    protected $bot;
    protected $telegram;
    protected $user;

    private function typeYourEmail()
    {
        $question = Question::create('Please type your email.')
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason');

        return $this->ask($question, function (Answer $answer) {
            $email = $answer->getText();
            $validator = Validator::make(
                ['email' => $email],
                ['email' => 'email']
            );

            if (!$validator->passes()) {
                $this->say('I need a valid email :-/');
                return $this->typeYourEmail();
            }

            $alreadyInUse = User::whereEmail($email)
                ->where('telegram_id', '<>', $this->user->telegram_id)
                ->first();

            if ($alreadyInUse) {
                $this->say('Hey... this email is subscribed already. Try another one or... maybe WAIT for the notifications.');
                return $this->typeYourEmail();
            } 
            $this->user->update(['email' => $email]);
            $this->say('I got your email.');

            return $this->bot->startConversation(new HowFarInAdvanceYouWishToBeNotified);
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

        $this->typeYourEmail();
    }
}