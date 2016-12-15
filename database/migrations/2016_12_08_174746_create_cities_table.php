<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities',function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('geoname_id')->null;
            $table->string('locale', 2);
            $table->string('continent_code', 2);
            $table->string('continent_name');
            $table->string('country_iso_code', 2);
            $table->string('country_name');
            $table->string('subdivision_1_iso_code', 3);
            $table->string('subdivision_1_name');
            $table->string('subdivision_2_iso_code', 3);
            $table->string('subdivision_2_name');
            $table->string('city_name');
            $table->string('metro_code');
            $table->string('time_zone');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
