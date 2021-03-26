<?php

namespace App\Formatters\NearEarthObjects;

use App\Models\NearEarthObjects\NearEarthObject;

trait Neo
{
    public function format(NearEarthObject $neo)
    {
        $name              = $neo->name;
        $approachDate      = $neo->approach_date;
        $estimatedDiameter = number_format($neo->estimated_diameter, 2, ',', '.');
        $relativeVelocity  = number_format($neo->relative_velocity, 2, ',', '.');
        $massDistance      = number_format($neo->mass_distance, 2, ',', '.');
        $infoUrl           = $neo->info_url;

        return 
            <<<TXT
            Name:                 {$name} \n
            Approach date:        {$approachDate} \n
            Diameter (estimated): {$estimatedDiameter} Km \n
            Relative velocity:    {$relativeVelocity} Km/h \n
            Distance:             {$massDistance} Km \n
            More info:            {$infoUrl}
TXT;
    }
}