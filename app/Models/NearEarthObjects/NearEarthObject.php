<?php

namespace App\Models\NearEarthObjects;

use Illuminate\Database\Eloquent\Model;

class NearEarthObject extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'provider_id', 'name', 'info_url', 'hazardous', 'estimated_diameter',
        'relative_velocity', 'mass_distance', 'approach_date'
    ];
}
