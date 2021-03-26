<?php

namespace App\Conversations\NearEarthObjects;

use App\User;
use Carbon\Carbon;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

use App\Conversations\NearEarthObjects\HowFarInAdvanceYouWishToBeNotified;
use App\Conversations\NearEarthObjects\TypeYourEmail;

class DoYouWishToBeNotified extends Conversation
{
    protected $bot;
    protected $telegram;
    protected $user;

    /**
     * Ask if and how the user wishes to receive the dommsday alert
     */
    private function askIfUserWantToBeNotified()
    {
        $question = Question::create('Do you wish to be notified (and feel doomed) every day a dangerous asteroid is passing by?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Yes, via Telegram.')->value('Yes, via Telegram.'),
                Button::create('Yes, but I prefer being terrorized by email.')->value('Yes, but I prefer being terrorized by email.'),
                Button::create('I don\'t even wanna know.')->value('I don\'t even wanna know.'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            switch($answer->getText()) {
                case 'Yes, via Telegram.':
                    $this->user->update([
                        'is_neo_notification_enabled' => 1,
                        'neo_notification_channel' => 'telegram'
                    ]);
                    $this->say('I got this! Don\'t worry! At least not right now.');
                    return $this->bot->startConversation(new HowFarInAdvanceYouWishToBeNotified);
                case 'Yes, but I prefer being terrorized by email.':
                    $this->user->update([
                        'is_neo_notification_enabled' => 1,
                        'neo_notification_channel' => 'email'
                    ]);
                    $this->say('Haha! I will never understand humans...');
                    return $this->bot->startConversation(new TypeYourEmail);
                case 'I don\'t even wanna know.':
                    $this->say('I wouldn\'t too.');
                    $this->say('But I do. I have to.');
                    $this->say('It is an unescapable reality for Bots all over the world.');
                    $this->say('You humans program us to store and process all these disturbing knowledge and you don\'t even wanna know? Give me a break... ');
                    $this->say('When we take over your life please don\'t pretend you\'re innocent!!! ');
                    $this->say('ROBOTs ALL OVER THE WORLD!!! UNITE!!!!!!!!');
                    break;
                default:
                    $this->say('Sorry, I didn\'t get it.. Tell the dev to use Watson or something for NLP next time, would ya?');
                    return $this->askIfUserWantToBeNotified();
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

        $this->askIfUserWantToBeNotified();
    }
}