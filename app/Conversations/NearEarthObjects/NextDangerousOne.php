<?php

namespace App\Conversations\NearEarthObjects;

use App\User;
use Carbon\Carbon;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

use App\Conversations\NearEarthObjects\DoYouWishToBeNotified;
use App\Formatters\NearEarthObjects\Neo as NeoFormatter;
use App\Services\NearEarthObjects\NeoServiceInterface as NearEarthObjectsServiceInterface;
use App\Repositories\NearEarthObjects\NeoRepositoryInterface as NearEarthObjectsRepositoryInterface;

class NextDangerousOne extends Conversation
{
    use NeoFormatter;

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

        return $this->showTheNextDangerous();
    }

    private function showTheNextDangerous()
    {
        $question = Question::create('For the sake of curiosity, do you wanna know if there are any danger for the next 7 days?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Yes.')->value('Yes.'),
                Button::create('No.')->value('No.'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            switch($answer->getText()) {
                case 'Yes.':
                    $from = Carbon::now();
                    $until = Carbon::now()->addDays(7);

                    // Check the database
                    $neoRepository = app()->make(NearEarthObjectsRepositoryInterface::class);
                    $nearEarthObjects = $neoRepository->getHazardous($from, $until);

                    if (!$nearEarthObjects->count()) {
                        // Fallback to live fetching, just in case scheduled fetching isn't going as expected.
                        $neoService = app()->make(NearEarthObjectsServiceInterface::class);
                        $neoService->fetch();
                        $nearEarthObjects = $neoRepository->getHazardous($from, $until);
                    }

                    if ($nearEarthObjects->count()) {
                        $this->say('Well, I think you should be concerned. Take a look:');
                        $nextDangerousNeo = $nearEarthObjects->first();
                        $this->say($this->format($nextDangerousNeo));
                        return $this->bot->startConversation(new DoYouWishToBeNotified);
                    }

                    $this->say('Well.. this is weird.  Usually there is a dangeorus asteroid approaching.');
                    $this->say('I could be defective, though.');
                    $this->say('Anyway...');
                    return $this->bot->startConversation(new DoYouWishToBeNotified);

                case 'No.':
                    $this->say('You\'re kinda boring, you know?');
                    return $this->say('Well, whatever...');
                default:
                    $this->say('Sorry, I didn\'t get it.. Tell the dev to use Watson or something for NLP next time, would ya?');
                    return $this->showTheNextDangerous();
            }
        });
    }
}