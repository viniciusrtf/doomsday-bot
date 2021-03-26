<?php

namespace Tests\BotMan;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

use App\User;
use App\Models\NearEarthObjects\NearEarthObject;

class NoRandomChattingPleaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test random chatting, than test option "Yes"
     *
     * @return void
     */
    public function testNoRandomChattingThanYes()
    {
        $this->bot
            ->receives(Str::random(40))
            ->assertReply('I\'m a very focused Bot and I don\'t like being disturbed with random chatting.')
            ->reply(5)
            ->receives('Yes.')
            ->assertReply('Looking forward to receive some BAD news? Haha, you humans are funny. Ok then, let me see if there\'s any...');
    }

    /**
     * Test random chatting, than test option "No"
     *
     * @return void
     */
    public function testRandomChattingThanNo()
    {
        $this->bot
            ->receives(Str::random(40))
            ->assertReply('I\'m a very focused Bot and I don\'t like being disturbed with random chatting.')
            ->reply(5)
            ->receives('No.')
            ->assertReply('What a pest you are. Bye.');
    }
}