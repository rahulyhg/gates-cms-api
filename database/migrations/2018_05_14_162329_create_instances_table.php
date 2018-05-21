<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instances', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('year');
            $table->integer('month');
            $table->date('date');
            $table->string('state_abr');
            $table->string('crime_type');
            $table->integer('crimeCount');

            $table->double('lat', 9, 6);
            $table->double('long', 9, 6);

            $table->integer('tract_id');

            $table->integer('population');
            
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
        Schema::dropIfExists('instances');
    }
}
