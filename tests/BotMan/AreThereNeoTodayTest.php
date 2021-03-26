<?php

namespace Tests\BotMan;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Models\NearEarthObjects\NearEarthObject;

class AreThereNeoTodayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the main phrase is answered correctly when there ARE dangerous asteroids
     *
     * @return void
     */
    public function testAreThereNeoTodayYes()
    {
        factory(User::class)->make()->save();
        factory(NearEarthObject::class)->make(['approach_date' => Carbon::now()->toDateString()])->save();

        $this->bot
            ->receives('Are there any asteroid endangering Earth today?')
            ->assertReply('Looking forward to receive some BAD news? Haha, you humans are funny. Ok then, let me see if there\'s any...')
            ->assertReply('Well, I guess your desire for caos and destruction could be fulfilled. Take a look:');
    }

    /**
     * Test if the main phrase is answered correctly when there are NOT dangerous asteroids
     *
     * @return void
     */
    public function testAreThereNeoTodayNo()
    {
        factory(User::class)->make()->save();
        factory(NearEarthObject::class)->make(['approach_date' => Carbon::now()->subDays(1)->toDateString()])->save();

        $this->bot
            ->receives('Are there any asteroid endangering Earth today?')
            ->assertReply('Looking forward to receive some BAD news? rHaha, you humans are funny. Ok then, let me see if there\'s any...')
            ->assertReply('You\'re in luck! There are no dangerous asteroids approaching your beautiful planet today.');
    }
}
