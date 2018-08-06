<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meteor extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nasa_id', 'name', 'nasa_url', 'hazardous', 'estimated_diameter',
        'relative_velocity', 'mass_distance', 'approach_date'
    ];
}
