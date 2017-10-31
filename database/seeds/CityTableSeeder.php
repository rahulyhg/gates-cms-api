<?php

use App\Models\City;  
use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      for ($i = 1; $i < 51; $i++):
        City::create([
            'title' => 'Title ' . $i,
            'slug' => 'title-' . $i,
            'body' => $i
        ]);
      endfor;
    }
}
