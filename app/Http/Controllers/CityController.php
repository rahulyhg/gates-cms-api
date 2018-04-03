<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Data;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Config;

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

      $request_timespans = $request->input('timespans', array());
      if (count($request_timespans) == 0) $request_timespans = array($request->input('gettimespans', array()));
      if (count($request_timespans) == 0) return response('Invalid Time Format', 400);
      // if (count($request_timespans) == 1) $request_timespans = array($request_timespans);

      $city_ids = $request->input('cityIds', array());

      $format = $request->input('format', 'csv');

      $count = 0;
      $responseArray = array();
      forEach($request_timespans as $timespan) {
        $timespans = gettype($timespan) == "array" ? $timespan : explode('/', $timespan);
        if (count($timespans) != 2) return response('Invalid Time Format - 2', 400);

        //YYYY-MM-DDT13:00:00Z/YYYY-MM-DDT15:30:00Z

        $begin = date ('Y-m-d 00:00:00', strtotime ($timespans[0]) );
        $end = date ('Y-m-d 00:00:00', strtotime ($timespans[1]) );

        $yearData = filter_var($request->input('yearData', false), FILTER_VALIDATE_BOOLEAN);

        // $cities = City::with(array('data' => function ($query) use ($begin, $end) {
        //   $query->where('datatype', '=', 1);
        //   $query->where('date', '>=', $begin); 
        //   $query->where('date', '<=', $end); 
        //   $query->orderBy('date', 'asc');
        // }, 'state'))->get(['county', 'id', 'title', 'state_id']);

        // $data = Data::with('city', 'state')
        $data = count($city_ids) > 0 ? Data::where('datatype', '=', $yearData ? 1 : 2)
          ->where('date', '>=', $begin)
          ->where('date', '<=', $end)
          ->whereIn('city_id', $city_ids)
          ->with(['city:id,title,county,state_id', 'city.state:id,abbreviation,title', 'crime:id,name', 'source:id,name'])
          ->orderBy('date', 'asc')
          ->get(['id','city_id','crimeCount','crime_id','date','per100k','population','source_id'])
        :
          Data::where('datatype', '=', $yearData ? 1 : 2)
          ->where('date', '>=', $begin)
          ->where('date', '<=', $end)
          ->with(['city:id,title,county,state_id', 'city.state:id,abbreviation,title', 'crime:id,name', 'source:id,name'])
          ->orderBy('date', 'asc')
          ->get(['id','city_id','crimeCount','crime_id','date','per100k','population','source_id']);


        $data = $data->toArray();
        $count += count($data);
        $mapping = function ($value) use ($request_timespans, $begin, $end, $yearData) {
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
          $newVal['crime_rate_per_100k'] = $value['per100k'];
          $newVal['source_desc'] = $value['source']['name'];
          return $newVal;
        };
        $data = array_map($mapping, $data);
        $responseArray = array_merge($responseArray, $data);
      }

      $headers = [
              'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
          ,   'Expires'             => '0'
          ,   'Pragma'              => 'public',
          'Access-Control-Allow-Origin'      => '*'
        ];

      if ($format == 'xlsx'):
        $headers['Content-type'] = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
        $headers['Content-Disposition'] = "attachment; filename=export.xlsx";
        Excel::create('Laravel Excel', function($excel) use ($responseArray) {
            $excel->sheet('Excel sheet', function($sheet) use ($responseArray) {

                $sheet->setOrientation('landscape');
                $sheet->fromArray($responseArray);

            });
        })->download('xls', $headers);
        // return response()->json(array('data'=>$responseArray));
      else:
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
        // return response()->stream($callback, 200, $headers);
        // return response()->json(array('data'=>$responseArray));
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

        $yearData = filter_var($request->input('yearData', false), FILTER_VALIDATE_BOOLEAN);
        // if ($yearData) {
        //   $begin = explode('-', $begin);
        //   $begin[1] = '01';
        //   $begin[2] = '01 00:00:00';
        //   $begin = implode('-', $begin);

        //   $end = explode('-', $end);
        //   $end[1] = '01';
        //   $end[2] = '01 00:00:00';
        //   $end = implode('-', $end);
        // }

        // $cities = City::with('data')->get(['id']);

        if ($yearData) {
          $cities = City::with(array('data' => function ($query) use ($begin, $end) {
            $query->where('datatype', '=', 1);
            $query->where('date', '>=', $begin); 
            $query->where('date', '<=', $end); 
            $query->orderBy('date', 'asc');
          }))->get(['id']);
        } else {
          $cities = City::with(array('data' => function ($query) use ($begin, $end){
            $query->where('datatype', '=', 2); 
            $query->where('date', '>=', $begin); 
            $query->where('date', '<=', $end); 
            $query->orderBy('date', 'asc');
          }))->get(['id']);
        }
        forEach($cities->toArray() as $i => $city) {
          if ( count ($city["data"]) == 0) continue;
          if (!isset($responseArray[$city["id"]])) {
            $responseArray[$city["id"]] = array();
            $count ++;
          }
          $crimes = 0;
          $population = 0;
          $change = 0;
          forEach($city["data"] as $i=>$data) {
            $crimes += $data["crimeCount"];
            $population += $data["population"];
            $change += ($data["crimeCount"] * 100000) / $data["population"];
          }
          $responseArray[$city["id"]][] = array(
            "population"=> floor($population / count($city["data"])),
            "crimes"=> $crimes,
            "timespan"=> $timespan,
            "rate"=> round(($change / count($city["data"]) * ($yearData ? 1 : 12)), 4)
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