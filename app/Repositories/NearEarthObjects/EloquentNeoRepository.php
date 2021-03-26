<?php

namespace App\Repositories\NearEarthObjects;

use App\Repositories\NearEarthObjects\NeoRepositoryInterface;
use App\Models\NearEarthObjects\NearEarthObject;
use Carbon\Carbon;

class EloquentNeoRepository implements NeoRepositoryInterface
{
    protected $meteor;

    public function __construct()
    {
        $this->neoQuery = new NearEarthObject();
    }

    /**
     * Fetch all "Near Earh Objects" from database
     *
     * @return Collection
     */
    public function getHazardous(Carbon $from = null, Carbon $until = null)
    {
        if (!empty($from)) {
            $this->neoQuery = $this->neoQuery->where('approach_date', '>=', $from->format('Y-m-d') );
        }

        if (!empty($until)) {
            $this->neoQuery = $this->neoQuery->where('approach_date', '<=', $until->format('Y-m-d') );
        }
        
        $this->neoQuery = $this->neoQuery->orderBy('approach_date', 'ASC');

        return $this->neoQuery->get();
    }
}
