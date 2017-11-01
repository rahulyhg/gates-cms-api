<?php

use App\Models\State;  
use App\Models\City;  
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = Faker::create();
      $states = State::all();

      for ($i = 1; $i < 201; $i++):
        $state = $states[rand(0, count($states) - 1)];
        $city = $faker->city;
        City::create([
            'title' => $city,
            'slug' => str_slug($city),
            'photo' => '/img/city-2.png',
            'state_id' => $state->id,
            'body' => $faker->text(500)
        ]);
      endfor;
    }
}
