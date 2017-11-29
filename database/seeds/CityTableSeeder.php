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
            'county' => $faker->city . ' County',
            'photo' => 'usl33aogxpfidj7tz0lo',
            'state_id' => $state->id,
            'populationGroup' => rand(1,3),
            'body' => $faker->text(500),
            'lat' => $faker->latitude(),
            'long' => $faker->longitude()
        ]);
      endfor;
    }
}
