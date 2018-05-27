<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Data;
use App\Models\Media;
use App\Models\Sheet;
use App\Models\Tract;
use App\Models\County;
use App\Models\Instance;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;
use ZipArchive;
use Zipper;

class CityController extends Controller
{
    /**
     * Get the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function api()
    {
      $cities = City::with('state')->get(['id', 'title as name', 'lat', 'long', 'state_id']);
      return response()->json(array('data'=> $cities), 200, [], JSON_NUMERIC_CHECK);
    }    

    /**
     * Get the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function tracts(Request $request)
    {
      $city_ids = $request->input('cityIds', array());
      if (gettype($city_ids) == 'string') {$city_ids = explode(',', $city_ids);}
      if(count($city_ids) == 0) return response('Invalid CityIds', 400);
                  // return response()->json(array('data'=> $city_ids), 200, [], JSON_NUMERIC_CHECK);


      $cities = City::whereIn('id', $city_ids)
      ->with([
        'counties', 
        'counties.tracts',
      ])
      // ->with(['counties:id', 'counties.tracts:id'])
      ->get(['id']);

      $tractIds = [];
      foreach($cities as $city) {
        foreach($city["counties"] as $county) {
          foreach($county["tracts"] as $tract) {
            $tractIds[$tract["id"]] = true;
          }
        }
      }

      return response()->json(array('data'=> array_keys($tractIds)), 200, [], JSON_NUMERIC_CHECK);
    }

    public function timespan()
    {
      $first = Data::orderBy('date', 'ASC')->first();

      // die();
      $last = Data::orderBy('date', 'DESC')->first();
      $spans = [
        $first['date']."T00:00:00Z",
        $last['date']."T00:00:00Z"
      ];
      return response()->json(array('data'=>$spans), 200, [], JSON_NUMERIC_CHECK);
    }

   /**
     * exporting.
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {

      $includeData = filter_var($request->input('includeData', true), FILTER_VALIDATE_BOOLEAN);
      $includeMeta = filter_var($request->input('includeMeta', false), FILTER_VALIDATE_BOOLEAN);

      $request_timespans = $request->input('timespans', array());
      if (count($request_timespans) == 0 && $includeData) $request_timespans = array($request->input('gettimespans', array()));
      if (count($request_timespans) == 0 && $includeData) return response('Invalid Time Format', 400);
      if (count($request_timespans) == 1 && $includeData) $request_timespans = array($request_timespans);

      $city_ids = $request->input('cityIds', array());
      $tract_ids = $request->input('tractIds', array());

      if (gettype($city_ids) == 'string') {$city_ids = explode(',', $city_ids);}
      if (gettype($tract_ids) == 'string') {$tract_ids = explode(',', $tract_ids);}

      $format = $request->input('format', 'csv');
      
      $yearData = filter_var($request->input('yearData', false), FILTER_VALIDATE_BOOLEAN);

      $count = 0;
      $responseArray = array();

      if ($includeData):
        forEach($request_timespans as $timespan) {
          $timespans = gettype($timespan) == "array" ? $timespan : explode('/', $timespan);

          if (count($timespans) != 2 && $includeData) return response('Invalid Time Format - 2', 400);

          //YYYY-MM-DDT13:00:00Z/YYYY-MM-DDT15:30:00Z

          $begin = date ('Y-m-d 00:00:00', strtotime ($timespans[0]) );
          $end = date ('Y-m-d 00:00:00', strtotime ($timespans[1]) );


          // $cities = City::with(array('data' => function ($query) use ($begin, $end) {
          //   $query->where('datatype', '=', 1);
          //   $query->where('date', '>=', $begin); 
          //   $query->where('date', '<=', $end); 
          //   $query->orderBy('date', 'asc');
          // }, 'state'))->get(['county', 'id', 'title', 'state_id']);

          // $data = Data::with('city', 'state')
          if (count($city_ids) > 0) {
            $data = Data::where('datatype', '=', $yearData ? 1 : 2)
              ->where('date', '>=', $begin)
              ->where('date', '<=', $end)
              ->whereIn('city_id', $city_ids)
              ->with(['city:id,title,county,state_id', 'city.state:id,abbreviation,title', 'crime:id,name', 'source:id,name'])
              ->orderBy('date', 'asc')
              ->get(['id','city_id','crimeCount','crime_id','date','per100k','population','source_id']);
          } elseif (count($tract_ids) > 0) {
            $data = Instance::where('date', '>=', $begin)
              ->where('date', '<=', $end)
              ->whereIn('tract_id', $tract_ids)
              // ->with(['tract'])
              ->orderBy('date', 'asc')
              ->get(['year', 'month', 'date', 'state_abr', 'crime_type', 'crimeCount', 'lat', 'long', 'tract_id', 'population']);
            // return response()->json(array('data'=>$data));
          } else {
            $data = Data::where('datatype', '=', $yearData ? 1 : 2)
              ->where('date', '>=', $begin)
              ->where('date', '<=', $end)
              ->with(['city:id,title,county,state_id', 'city.state:id,abbreviation,title', 'crime:id,name', 'source:id,name'])
              ->orderBy('date', 'asc')
              ->get(['id','city_id','crimeCount','crime_id','date','per100k','population','source_id']);
          }
   

          $data = $data->toArray();
          $count += count($data);
          $cityMapping = function ($value) use ($request_timespans, $begin, $end, $yearData) {
            $newVal = [];
            if (count($request_timespans) > 1) {
              $b = explode(' ', $begin);
              $e = explode(' ', $end);
              $newVal['timespan'] = $b[0]."T00:00:00Z/".$e[0]."T00:00:00Z";
            }
            $newVal['id'] = $value['city']['id'];
            $newVal['year'] = date ('Y', strtotime ($value['date']) );
            if (!$yearData) {
              $newVal['month'] = date ('m', strtotime ($value['date']) );
            }
            $newVal['state_abr'] = $value['city']['state']['abbreviation'];
            $newVal['county_name'] = $value['city']['county'];
            $newVal['place_name'] = $value['city']['title'];
            $newVal['population_est'] = $value['population'];
            $newVal['crime_type'] = $value['crime']['name'];
            $newVal['crime_count'] = $value['crimeCount'];
            $newVal['annualized_rate_per_100k'] = $value['per100k'];
            $newVal['source_desc'] = $value['source']['name'];
            return $newVal;
          };

          $tractMapping = function ($value) use ($request_timespans, $begin, $end, $yearData) {
            $newVal = [];
            if (count($request_timespans) > 1) {
              $b = explode(' ', $begin);
              $e = explode(' ', $end);
              $newVal['timespan'] = $b[0]."T00:00:00Z/".$e[0]."T00:00:00Z";
            }
            // $newVal['geoid'] = $value['tract']['id'];
            $newVal['year'] = $value['year'];
            $newVal['month'] = $value['month'];
            $newVal['population_est'] = $value['population'];
            // $newVal['crime_type'] = $value['crime']['name'];
            $newVal['crime_count'] = $value['crimeCount'];
            return $value;
          };

          $data = count($tract_ids) > 0 ? array_map($tractMapping, $data) : array_map($cityMapping, $data);
          $responseArray = array_merge($responseArray, $data);
        }

      endif;

      $headers = [
              'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
          ,   'Expires'             => '0'
          ,   'Pragma'              => 'public',
          'Access-Control-Allow-Origin'      => '*'
        ];

      if ($format == 'xlsx' && $includeData && !$includeMeta):
        $headers['Content-type'] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
        $headers['Content-Disposition'] = "attachment; filename=export.xlsx";
        Excel::create('Laravel Excel', function($excel) use ($responseArray) {
            $excel->sheet('Excel sheet', function($sheet) use ($responseArray) {

                $sheet->setOrientation('landscape');
                $sheet->fromArray($responseArray);

            });
        })->download('xls', $headers);
      elseif($format == 'csv' && $includeData && !$includeMeta):
        $headers['Content-type'] = "text/csv";
        $headers['Content-Disposition'] = "attachment; filename=export.csv";

        if (count($responseArray) > 0) {
          array_unshift($responseArray, array_keys($responseArray[0]));
        }
        
        $callback = function() use ($responseArray) {
          $FH = fopen('php://output', 'w');
          foreach ($responseArray as $row) { 
              fputcsv($FH, $row);
          }
          fclose($FH);
        };

        $response = new StreamedResponse($callback, 200, $headers);

        return $response;
      elseif($includeData && $includeMeta):

        $zipper = new Zipper;
        $zip = storage_path("zips/export.zip");
        $zipper->make($zip);
        $zipper->remove($zipper->listFiles());

        $metadata = Sheet::where('type', 'Metadata')
        ->orderBy('created_at', 'desc')
        ->firstOrFail();

        $source = "https://res.cloudinary.com/gates/raw/upload/" . $metadata->data;
        $data = file_get_contents($source);
        $zipper->addString("metadata.pdf", $data);

        if ($format === "csv") {
          if (count($responseArray) > 0) {
            array_unshift($responseArray, array_keys($responseArray[0]));
          }
          ob_start();
          $data = fopen('php://output', 'w');
          foreach ($responseArray as &$row) { 
            implode(",", $row);
              fputcsv($data, $row);
          }
          $data = ob_get_contents();
          ob_end_clean();
          $zipper->addString("data.csv", $data);
        } else {
          $stored = Excel::create('data', function($excel) use ($responseArray) {
              $excel->sheet('data', function($sheet) use ($responseArray) {
                  $sheet->setOrientation('landscape');
                  $sheet->fromArray($responseArray);
              });
          })->store('xlsx', false, true);
          $zipper->add($stored["full"]);
        }
        $zipper->close();
        return response()->download($zip, "export.zip", $headers);

      elseif(!$includeData && $includeMeta):

        $headers = [
            'Access-Control-Allow-Origin'      => '*',
            'Access-Control-Allow-Methods'     => 'POST, GET, OPTIONS, PUT, DELETE, PATCH',
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age'           => '86400',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With'
        ];

        $metadata = Sheet::where('type', 'Metadata')
        ->orderBy('created_at', 'desc')
        ->firstOrFail();
        return redirect()->to("https://res.cloudinary.com/gates/raw/upload/" . $metadata->data, 302, $headers);

      endif;
    }

   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function crimes(Request $request)
    {

      $request_timespans = $request->input('timespans', array());
      if (count($request_timespans) == 0) $request_timespans = array($request->input('gettimespans', array()));
      if (count($request_timespans) == 0) return response('Invalid Time Format', 400);
      // if (count($request_timespans) == 1) $request_timespans = array($request_timespans);
      if (gettype($request_timespans) == 'string') {$request_timespans = explode(',', $request_timespans);}

      $city_ids = $request->input('cityIds', array());
      $tract_ids = $request->input('tractIds', array());
      if (gettype($city_ids) == 'string') {$city_ids = explode(',', $city_ids);}
      if (gettype($tract_ids) == 'string') {$tract_ids = explode(',', $tract_ids);}

      $yearData = filter_var($request->input('yearData', false), FILTER_VALIDATE_BOOLEAN);


      $incompleteAllowed = filter_var($request->input('incompleteAllowed', true), FILTER_VALIDATE_BOOLEAN);
      $count = 0;
      $responseArray = array();
      // print_r($request_timespans);

      forEach($request_timespans as $timespan) {

        $timespans = explode('/', $timespan);
        if (count($timespans) != 2) return response('Invalid Time Format - 2', 400);

        //2007-10-01T13:00:00Z/2008-05-11T15:30:00Z


        $begin = date ('Y-m-d 00:00:00', strtotime ($timespans[0]) );
        $end = date ('Y-m-d 00:00:00', strtotime ($timespans[1]) );
       
        $begin = new \DateTime($begin);
        $end = new \DateTime($end);



        if ($yearData) {
          $years = $begin->diff($end)->y;
          // $years == 0 ? 1 : $years;

          $cityPerYear = [];
          for ($i = 0; $i <= $years; $i++) {
            $_begin = clone $begin;
            $_begin->add(new \DateInterval('P'.$i.'Y'));
            $_end = clone $begin;
            $_end->add(new \DateInterval('P'.($i + 1).'Y'));
            if (count($city_ids) > 0) {
              $cities = City::with(array('data' => function ($query) use ($_begin, $_end) {
                $query->where('datatype', '=', 1);
                $query->where('date', '>=', $_begin->format('Y-m-d')); 
                $query->where('date', '<', $_end->format('Y-m-d')); 
                $query->orderBy('date', 'asc');
              }))
              ->whereIn('id', $city_ids)
              ->groupBy('id')
              ->get(['id']);

            } elseif (count($tract_ids) > 0) {
              $cities = Tract::with(array('instance' => function ($query) use ($_begin, $_end) {

                $query->where('date', '>=', $_begin->format('Y-m-d')); 
                $query->where('date', '<', $_end->format('Y-m-d')); 
                $query->orderBy('date', 'asc');
              }))
              ->whereIn('id', $tract_ids)
              ->groupBy('id')
              ->get(['id']);
            } else {
              $cities = City::with(array('data' => function ($query) use ($_begin, $_end) {
                $query->where('datatype', '=', 1);
                $query->where('date', '>=', $_begin->format('Y-m-d')); 
                $query->where('date', '<', $_end->format('Y-m-d')); 
                $query->orderBy('date', 'asc');
              }))
              ->groupBy('id')
              ->get(['id']);
            }
            $cityPerYear[] = $cities->toArray();
          }
          $incompleteCities = [];
          $cities = [];
          forEach($cityPerYear as $_cities) {
            forEach($_cities as $city) {
              if (isset($city["data"])):
                $key = "data";
              else:
                $key = "instance";
              endif;

              if (count($city[$key]) === 0) $incompleteCities[$city["id"]] = true;
              if(!isset($cities[$city["id"]])) {
                $cities[$city["id"]] = $city;
                $cities[$city["id"]][$key] = [];
              }
              $cities[$city["id"]][$key] = array_merge($cities[$city["id"]][$key], $city[$key]);
            }
          }
          $incompleteCities = array_keys($incompleteCities);
        } else {
          $months = $begin->diff($end)->m + ($begin->diff($end)->y*12);
          // $months == 0 ? 1 : $months;
          $cityPerMonth = [];
          for ($i = 0; $i <= $months; $i++) {
            $_begin = clone $begin;
            $_begin->add(new \DateInterval('P'.$i.'M'));
            $_end = clone $begin;
            $_end->add(new \DateInterval('P'.($i + 1).'M'));

            if (count($city_ids) > 0) {
              
              $cities = City::with(array('data' => function ($query) use ($_begin, $_end){
                $query->where('datatype', '=', 2); 
                $query->where('date', '>=', $_begin->format('Y-m-d')); 
                $query->where('date', '<', $_end->format('Y-m-d')); 
                $query->orderBy('date', 'asc');
              }))
              ->whereIn('id', $city_ids)
              ->groupBy('id')
              ->get(['id']);
            } elseif (count($tract_ids) > 0) {

              $cities = Tract::with(array('instance' => function ($query) use ($_begin, $_end) {
                $query->where('date', '>=', $_begin->format('Y-m-d')); 
                $query->where('date', '<', $_end->format('Y-m-d')); 
                $query->orderBy('date', 'asc');
              }))
              ->whereIn('id', $tract_ids)
              ->groupBy('id')
              ->get(['id']);
            } else {
              $cities = City::with(array('data' => function ($query) use ($_begin, $_end){
                $query->where('datatype', '=', 2); 
                $query->where('date', '>=', $_begin->format('Y-m-d')); 
                $query->where('date', '<', $_end->format('Y-m-d')); 
                $query->orderBy('date', 'asc');
              }))
              ->groupBy('id')
              ->get(['id']);
            }

            $cityPerMonth[] = $cities->toArray();
          }
          $incompleteCities = [];
          $cities = [];

          forEach($cityPerMonth as $_cities) {
            forEach($_cities as $city) {
              if (isset($city["data"])):
                $key = "data";
              else:
                $key = "instance";
              endif;
              if (count($city[$key]) === 0) $incompleteCities[$city["id"]] = true;
              if(!isset($cities[$city["id"]])) {
                $cities[$city["id"]] = $city;
                $cities[$city["id"]][$key] = [];
              }
              $cities[$city["id"]][$key] = array_merge($cities[$city["id"]][$key], $city[$key]);
            }
          }
          $incompleteCities = array_keys($incompleteCities);
        }
        forEach($cities as $i => $city) {
          if (in_array($city["id"], $incompleteCities)) continue;
          if (isset($city["data"])):
            $key = "data";
          else:
            $key = "instance";
          endif;
          if ( count ($city[$key]) == 0) continue;
          if (!isset($responseArray[$city["id"]])) {
            $responseArray[$city["id"]] = array();
            $count ++;
          }
          $crimes = 0;
          $population = 0;
          $change = 0;
          forEach($city[$key] as $i=>$data) {
            $crimes += intval(str_replace(",", "", $data["crimeCount"]));
            $population += intval(str_replace(",", "", $data["population"]));
            $change += (intval(str_replace(",", "", $data["crimeCount"])) * 100000) / intval(str_replace(",", "", $data["population"]));
          }
          $responseArray[$city["id"]][] = array(
            "population"=> floor($population / count($city[$key])),
            "crimes"=> $crimes,
            "timespan"=> $timespan,
            "rate"=> round(($change / count($city[$key]) * ($yearData ? 1 : 12)), 4)
          );
        }
      }
      if (!$incompleteAllowed) {
        $timespanCount = count($request_timespans);
        foreach($responseArray as $city=>$timespans) {
          if (count($timespans) != $timespanCount) {
            unset($responseArray[$city]);
          }
        }
      }

      if (count($responseArray) == 0) $responseArray = null;
      return response()->json(array('data'=>$responseArray));
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json(City::with('state')->get(), 200, [], JSON_NUMERIC_CHECK);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
        'title' => 'required',
        'county' => 'required',
        'slug' => 'required',
        'photo' => 'required',
        'populationGroup' => 'required',
        'state_id' => 'required',
        'body' => 'required',
        'long' => 'required',
        'lat' => 'required'
         ]);

        $city = new City();

        $city->slug = str_slug($request->input('slug'));
        $city->county = $request->input('county');
        $city->title = $request->input('title');
        $city->photo = $request->input('photo', $city->photo);
        $city->populationGroup = $request->input('populationGroup', $city->populationGroup);
        $city->state_id = $request->input('state_id', $city->state_id);
        $city->body = $request->input('body');
        $city->long = $request->input('long');
        $city->lat = $request->input('lat');

        $city->save();
        return response()->json($city, 201);
    }

    /**
     * Get the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return response()->json(City::with('state')->find($id));
    }




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

      $this->validate($request, [
      'title' => 'required',
      'slug' => 'required',
      'county' => 'required',
      'photo' => 'required',
      'populationGroup' => 'required',
      'state_id' => 'required',
      'body' => 'required',
      'long' => 'required',
      'lat' => 'required'
       ]);

      $city = City::find($id);
      $city->county = $request->input('county', $city->county);
      $city->slug = str_slug($request->input('slug', $city->slug));
      $city->photo = $request->input('photo', $city->photo);
      if ($city->photo) {
          $media = Media::where('cloudinary', $city->photo)
          ->update(['city_id'=> $id]);
      }
      $city->populationGroup = $request->input('populationGroup', $city->populationGroup);
      $city->state_id = $request->input('state_id', $city->state_id);
      $city->title = $request->input('title', $city->title);
      $city->body = $request->input('body', $city->body);
      $city->long = $request->input('long', $city->long);
      $city->lat = $request->input('lat', $city->lat);
      $city->save();
      return response()->json($city);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      City::find($id)->delete();
      return response('', 200);
    }

}
