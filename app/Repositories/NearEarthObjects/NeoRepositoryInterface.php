<?php

namespace App\Repositories\NearEarthObjects;

use Carbon\Carbon;

interface NeoRepositoryInterface
{
    /**
     * Fetch all "Near Earh Objects" from database
     *
     * @return Collection
     */
    public function getHazardous(Carbon $from = null, Carbon $until = null);
}
