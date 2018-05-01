<?php

use Illuminate\Support\Facades\Schema;
// use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Grimzy\LaravelMysqlSpatial\Schema\Blueprint;
class CreateTractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('STATEFP');
            $table->string('COUNTYFP');
            $table->string('TRACTCE');
            $table->string('GEOID');
            $table->string('NAME')->unique();;
            $table->string('NAMELSAD');
            $table->string('MTFCC');
            $table->string('FUNCSTAT');
            $table->string('ALAND');
            $table->string('AWATER');
            $table->string('INTPTLAT');
            $table->string('INTPTLON');
            $table->multiPolygon('area')->nullable();
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
        Schema::dropIfExists('tracts');
    }
}
