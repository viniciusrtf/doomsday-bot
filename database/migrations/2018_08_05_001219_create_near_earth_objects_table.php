<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNearEarthObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('near_earth_objects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('provider_id');
            $table->string('name');
            $table->string('info_url');
            $table->boolean('hazardous');
            $table->float('estimated_diameter', 20, 10);
            $table->float('relative_velocity', 20, 10);
            $table->float('mass_distance', 20, 10);
            $table->date('approach_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('near_earth_objects');
    }
}
