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
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('state_id');
            $table->integer('populationGroup');
            $table->string('county');
            $table->string('slug');
            $table->string('title');
            $table->string('photo');
            $table->text('body');
            $table->double('lat', 9, 6);
            $table->double('long', 9, 6);
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
