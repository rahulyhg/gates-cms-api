<?php
use App\Models\Tract;  
use Illuminate\Database\Seeder;
use Grimzy\LaravelMysqlSpatial\Types\Geometry;
use Grimzy\LaravelMysqlSpatial\Types\MultiPolygon;
use Grimzy\LaravelMysqlSpatial\Types\Point;

class TractTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $path = storage_path("tracts");
      $dir = new DirectoryIterator($path);
      foreach ($dir as $i => $fileinfo) {
          if ($i % 1000 == 0) echo $i . PHP_EOL;
          if (!$fileinfo->isDot() && $fileinfo->getFilename() !== ".DS_Store") {
              $string = file_get_contents($path . "/" . $fileinfo->getFilename());
              $tract = json_decode($string, true);
              // $area = Geometry::fromJson(json_encode($tract["geometry"]));
              $props = $tract["properties"];
              Tract::create([
                'id'=>$props["GEOID"], 
                'county_id'=>$props["STATEFP"].$props["COUNTYFP"], 
                'STATEFP'=>$props["STATEFP"], 
                'COUNTYFP'=>$props["COUNTYFP"], 
                'TRACTCE'=>$props["TRACTCE"], 
                'NAME'=>$props["NAME"], 
                'NAMELSAD'=>$props["NAMELSAD"], 
                'MTFCC'=>$props["MTFCC"],   
                'FUNCSTAT'=>$props["FUNCSTAT"], 
                'ALAND'=>$props["ALAND"], 
                'AWATER'=>$props["AWATER"], 
                'INTPTLAT'=>$props["INTPTLAT"], 
                'INTPTLON'=>$props["INTPTLON"], 
                // 'area'=>$area 
              ]);
          }
      }
      // $lat = 36.1104;
      // $long = -88.0980;
      // $query =  "select name, STATEFP, COUNTYFP, TRACTCE, GEOID, ST_Within(ST_PointFromText('POINT($long $lat)'), area) as within from tracts HAVING within=1;";
      // $result = DB::select(DB::raw($query));
      // if (count($result) > 0) {
      //   print_r($result[0]);
      // }

       // select tracts.*, ST_Within(ST_PointFromText('POINT(-88.0980 36.1104 )'), area) as within from tracts HAVING within=1;


    }
}
