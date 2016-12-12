<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCityCountryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_country', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            # city_id and country_id will be foreign keys, so they have to be unsigned
            $table->integer('city_id')->unsigned();
            $table->integer('country_id')->unsigned();

            # Make foregin keys
            $table->foreign('city_id')->references('id')->on('cities');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_country');
    }
}
