<?php

namespace App\Conversations\NearEarthObjects;

use App\User;
use Carbon\Carbon;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

use App\Conversations\NearEarthObjects\AreThereNeoToday;

class NoRandomChattingPlease extends Conversation
{
    protected $bot;
    protected $telegram;
    protected $user;

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

        return $this->noRandomChattingPlease();
    }

    private function noRandomChattingPlease()
    {
        $this->say('I\'m a very focused Bot and I don\'t like being disturbed with random chatting.');
        $this->say('I have a very specific purpose: I look through NASA\'s data to warn you (with some advance) that an asteroid may hit the Earth and annihilate your entire species.');
        $this->say('Not that you actually deserve this planet though...');
        $this->say('Well, please don\'t get my bedside manner wrong. I just bring facts. You humans... you bring "flat earth theory".');
        $this->say('That said, I\'m giving you a chance. That\'s because... well, because I\'m programmed to do so. Here it go, be a good human and don\'t waste it.');

        $question = Question::create('Do you want to know if a dangerous asteroid is passing by Earth today?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Yes.')->value('Yes.'),
                Button::create('No.')->value('No.'),
            ]);

            return $this->ask($question, function (Answer $answer) {
                switch($answer->getText()) {
                    case 'Yes.':
                        return $this->bot->startConversation(new AreThereNeoToday);
                    case 'No.':
                        return $this->say('What a pest you are. Bye.');
                    default:
                        return $this->noRandomChattingPlease();
                }
            });
    }
}
