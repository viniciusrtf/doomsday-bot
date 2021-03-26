<?php

namespace App\Services\NearEarthObjects;

interface NeoServiceInterface
{
    /**
     * Fetch "Near Earh Objects" within date range (max: 7 days)
     *
     * @return void
     */
    public function fetch();
}
