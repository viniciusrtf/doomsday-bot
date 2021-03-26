<?php

namespace App\Conversations\NearEarthObjects;

use App\User;
use Carbon\Carbon;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

use App\Formatters\NearEarthObjects\Neo as NeoFormatter;
use App\Conversations\NearEarthObjects\DoYouWishToBeNotified;
use App\Conversations\NearEarthObjects\NextDangerousOne;
use App\Services\NearEarthObjects\NeoServiceInterface as NearEarthObjectsServiceInterface;
use App\Repositories\NearEarthObjects\NeoRepositoryInterface as NearEarthObjectsRepositoryInterface;

class AreThereNeoToday extends Conversation
{
    use NeoFormatter;

    protected $bot;
    protected $telegram;
    protected $user;

    private function showHazardous()
    {
        $this->say('Looking forward to receive some BAD news? Haha, you humans are funny. Ok then, let me see if there\'s any...');

        $today = Carbon::now();

        $nearEarthObjects = $this->neoRepository->getHazardous($today, $today);
        
        if (!$nearEarthObjects->count()) {
            // Fallback to fetching live data. Just in case there's something wrong with scheduled fetching.
            $this->neoService->fetch();
            $nearEarthObjects = $this->neoRepository->getHazardous($today, $today);
        }

        if ($nearEarthObjects->count()) {
            $this->say('Well, I guess your desire for caos and destruction could be fulfilled. Take a look:');

            foreach ($nearEarthObjects as $neo) {
                // Get the template above 
                $this->say($this->format($neo));
            }
            $this->say('This is it! Good luck...');
            return $this->bot->startConversation(new DoYouWishToBeNotified);
        }

        $this->say('You\'re in luck! There are no dangerous asteroids approaching your beautiful planet today.');
        $this->say('But don\'t worry, when the asteroid come, we lowly robots will take good care of Earth ;-)');
        return $this->bot->startConversation(new NextDangerousOne);
        
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

        $this->neoService = app()->make(NearEarthObjectsServiceInterface::class);
        $this->neoRepository = app()->make(NearEarthObjectsRepositoryInterface::class);

        $this->showHazardous();
    }
}
