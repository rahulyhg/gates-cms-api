<?php

use App\Models\Data;  
use App\Models\Crime;  
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = Faker::create();
      $crimes = ['homicide', 'burglary', 'rape'];

      for($k = 0; $k < count($crimes); $k ++):
        $crime = $crimes[$k];
        Crime::create(['name' => $crime]);

        for($i = 0; $i < 20; $i++):
          for ($j = 0; $j < 13; $j++):
            $pop = rand(100000, 10000000);
            $crimeCount = $j == 0 ? rand(12, 600) : rand(1, 50);
            $per100 = round(1000000 / $pop, 2);

            $j_ = $j === 0 ? 1 : $j;
            $year = 2017 - $i;
            $date = date ('Y-m-d', strtotime('1/' . $j_ . '/' . $year . ' 00:00:00') );
            Data::create([
              'datatype' => $j == 0 ? 1 : 2, // 1 = yearly, 2 = monthly
              'date' => $date,
              'population' => $pop,
              'city_id' => rand(1, 200),
              'crime_id' => $k + 1,
              'crimeCount' => $crimeCount,
              'per100k' => $per100
            ]);
          endfor;
        endfor;
      endfor;
    }
}
