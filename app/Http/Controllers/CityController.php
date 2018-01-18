<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Data;
class CityController extends Controller
{
    /**
     * Get the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function api()
    {
      return response()->json(array('data'=> City::all(['id', 'title as name', 'lat', 'long'])), 200, [], JSON_NUMERIC_CHECK);
    }

    public function timespan()
    {
      $first = Data::orderBy('date', 'ASC')->first();

      // die();
      $last = Data::orderBy('date', 'DESC')->first();
      $spans = [
        $first['date'],
        $last['date']
      ];
      return response()->json(array('data'=>$spans), 200, [], JSON_NUMERIC_CHECK);
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
      if (count($request_timespans) == 1) $request_timespans = array($request_timespans);

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
        if ($yearData) {
          $begin = explode('-', $begin);
          $begin[1] = '01';
          $begin[2] = '01';
          $begin = implode('-', $begin);

          $end = explode('-', $end);
          $end[1] = '01';
          $end[2] = '01';
          $end = implode('-', $end);
        }

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
            // $query->where('date', '>=', 'DATE('.$begin.')'); 
            // $query->where('date', '<=', 'DATE('.$end.')'); 
            // $query->whereBetween('date', array('DATE('.$begin.')', 'DATE('.$end.')'));
            $query->where('date', '>=', $begin); 
            $query->where('date', '<', $end); 
            // $query->whereBetween('date', array($begin, $end);
            $query->orderBy('date', 'asc');
          }))->get(['id']);
        }

        forEach($cities->toArray() as $i => $city) {
          // $city["begin"] = $begin;
          // $city["end"] = $end;
          return response()->json(array('data'=>$city));
          if ($i > 0) continue;
          if ( count ($city["data"]) == 0) continue;
          if (!isset($responseArray[$city["id"]])) {
            $responseArray[$city["id"]] = array();
            $count ++;
            // print_r("<pre>" . $count . "</pre>");
          }
          $crimes = 0;
          $population = 0;
          $change = 0;
          forEach($city["data"] as $i=>$data) {
            $crimes += $data["crimeCount"];
            $population += $data["population"];
            $change += ($data["crimeCount"] * 100000) / $data["population"];
            // echo "<h4>crimes</h4><pre>";print_r($data["crimeCount"]);echo "</pre>";
            // echo "<h4>population</h4><pre>";print_r($data["population"]);echo "</pre>";
            // echo "<h4>rate</h4><pre>";print_r(($data["crimeCount"] * 100000) / $data["population"]);echo "</pre>";
          }
          $responseArray[$city["id"]][] = array(
            "population"=> floor($population / count($city["data"])),
            "crimes"=> $crimes,
            // "averageCrimes"=> round($crimes / count($city["data"]), 4),
            "timespan"=> $timespan,
            "rate"=> round($change / count($city["data"]), 4)
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