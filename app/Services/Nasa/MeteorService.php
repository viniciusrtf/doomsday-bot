<?php

namespace App\Services\Nasa;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Carbon\Carbon;
use Exception;
use App\Meteor;

class MeteorService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'timeout'  => 10,
            'base_uri' => config('meteor.nasa.base_uri'),
            'query'    => [
                'api_key'    => config('meteor.nasa.api_key'),
                'start_date' => Carbon::now()->toDateString(),
                'end_date'   => Carbon::now()->addDays(7)->toDateString()
            ]
        ]);
    }

    /**
     * Fetch "Near Earh Objects" within date range (max: 7 days)
     *
     * @throws Exception
     * @return void
     */
    public function fetch()
    {
        $response = $this->client->request('GET');

        // Mutate response to array
        $response = json_decode((string)$response->getBody(), true);

        // Extract meteors from grouped dates
        $meteors = [];
        foreach ($response['near_earth_objects'] as $dateGroupedMeteors) {
            foreach ($dateGroupedMeteors as $meteor) {
                $meteors[] = $meteor;
            }
        }

        // Filter to get only the hazardous meteors
        $meteors = array_filter($meteors, function($m) {
            return $m['is_potentially_hazardous_asteroid'];
        });

        foreach ($meteors as $meteor) {
            Meteor::updateOrCreate(
                ['nasa_id' => $meteor['neo_reference_id']],
                [
                    'nasa_id'   => $meteor['neo_reference_id'],
                    'name'      => $meteor['name'],
                    'nasa_url'  => $meteor['nasa_jpl_url'],
                    'hazardous' => $meteor['is_potentially_hazardous_asteroid'],
                    'estimated_diameter' => array_sum($meteor['estimated_diameter']['kilometers'])/2,
                    'relative_velocity'  => $meteor['close_approach_data'][0]['relative_velocity']['kilometers_per_hour'],
                    'mass_distance'      => $meteor['close_approach_data'][0]['miss_distance']['kilometers'],
                    'approach_date'      => $meteor['close_approach_data'][0]['close_approach_date']
                ]
            );
        }
    }
}
