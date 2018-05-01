<?php

use App\Models\County;  
use Illuminate\Database\Seeder;

class CountyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $path = storage_path("national_county.txt");
      $handle = fopen($path, "r");
      if ($handle) {
          while (($line = fgets($handle)) !== false) {
              $county = explode(",", $line);
              County::create([
                'id'=>$county[1].$county[2],
                'STATE'=>$county[0],
                'STATEFP'=>$county[1],
                'COUNTYFP'=>$county[2],
                'COUNTYNAME'=>$county[3],
                'CLASSFP'=>$county[4]
              ]);
          }

          fclose($handle);
      } else {
          // error opening the file.
      } 
    }
}
