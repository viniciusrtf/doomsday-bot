<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNotificationInfoColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_neo_notification_enabled')->nullable()->default(0);
            $table->string('neo_notification_channel')->nullable();
            $table->integer('neo_notification_days_in_advance')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $this->dropColumn('is_neo_notification_enabled');
            $this->dropColumn('neo_notification_channel');
            $this->dropColumn('neo_notification_days_in_advance');
        });
    }
}
