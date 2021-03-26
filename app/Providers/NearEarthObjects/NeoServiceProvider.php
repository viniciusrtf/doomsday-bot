<?php

namespace App\Providers\NearEarthObjects;

use Illuminate\Support\ServiceProvider;

class NeoServiceProvider extends ServiceProvider
{
    public function register()
    {
        app()->bind('App\Services\NearEarthObjects\NeoServiceInterface', 'App\Services\NearEarthObjects\NasaNeoService');
        app()->bind('App\Repositories\NearEarthObjects\NeoRepositoryInterface', 'App\Repositories\NearEarthObjects\EloquentNeoRepository');
    }
}