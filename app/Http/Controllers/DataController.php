<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Data;
use App\Models\State;
use App\Models\City;
use App\Models\Crime;
use App\Models\Source;
use App\Models\Media;
use App\Models\County;
use App\Models\Instance;

class DataController extends Controller
{ 

    public function dump(Request $request)
    {
      $type = $request->input('type');
      switch($type) {
        case('Data'):
          Data::truncate();
          break;
        case('States'):
          State::truncate();
          break;
        case('Cities'):
          City::truncate();
          break;
        case('Crimes'):
          Crime::truncate();
          break;
        case('Sources'):
          Source::truncate();
          break;
        case('Instances'):
          Instance::truncate();
          break;
      }
    }

    public function crimes (Request $request)
    {
       $this->validate($request, [
          '*.crime_descr' => 'required',
          '*.crime_type' => 'required'
         ]);
        $requests = $request->all();

        forEach($requests as $_request) {
          $crime = new Crime();
          $crime->id = $_request['crime_type'];
          $crime->name = $_request['crime_descr'];
          $crime->save();
        }
        return response()->json(null, 201);
    }

    public function sources (Request $request)
    {
       $this->validate($request, [
          '*.source_descr' => 'required',
          '*.source_id' => 'required'
         ]);
        $requests = $request->all();

        forEach($requests as $_request) {
          $source = new Source();
          $source->id = $_request['source_id'];
          $source->name = $_request['source_descr'];
          $source->save();
        }
        return response()->json(null, 201);
    }

    public function cities (Request $request)
    {
       $this->validate($request, [
          '*.id' => 'required',
          '*.state_fips' => 'required',
          '*.place_fips' => 'required',
          '*.place_name' => 'required',
          '*.county_name' => 'required',
          '*.longitude' => 'required',
          '*.latitude' => 'required',
          '*.population' => 'required'
         ]);
        $requests = $request->all();


        forEach($requests as $_request) {
          //  protected $fillable = ['id', 'title','slug','body','photo','state_id', 'populationGroup', 'county', 'long', 'lat'];


          $media = Media::where('city_id', $_request['id'])
          ->orderBy('created_at', 'desc')
          ->first();

          $city = new City();

          if ($media) {
            $city->photo = $media->cloudinary;
          }

          $city->id = $_request['id'];
          $name = $_request['place_name'];
          $name = str_replace(' city', '', $name);
          $name = str_replace(' metro', '', $name);
          $name = str_replace(' government', '', $name);
          $name = str_replace(' metropolitan', '', $name);
          $name = str_replace(' (balance)', '', $name);
          $name = str_replace(' municipality', '', $name);
          $name = str_replace(' urban county', '', $name);
          $city->title = $name;
          $city->slug = str_slug($name);
          $city->state_id = $_request['state_fips'];
          $city->county = $_request['county_name'];
          $city->long = $_request['longitude'];
          $city->lat = $_request['latitude'];
          $city->body = isset($_request['methodology']) ? $_request['methodology'] : '';
          $pop = intval(str_replace(",", "", $_request['population']));
          $city->populationGroup = $pop > 1000000 ? 3 : ($pop > 500000 ? 2 : 1);
          // populationGroups: [
          //   {value: 1, text: 'under 500k'},
          //   {value: 2, text: '500k - 1M'},
          //   {value: 3, text: 'over 1M'}
          // ],
          $city->save();
          $countyNames = array_map('trim', explode(',', $city->county));

          // return response()->json($countyNames, 201);
          $counties = County::where('STATEFP', $city->state_id)
          ->whereIn('COUNTYNAME', $countyNames)->pluck('id')->toArray();
          if (count($counties) > 0) {
            if (count($counties) !== count($countyNames)) {
              return response()->json(
                array(
                  'error' => 'missing counties', 
                  'countyNames' => $countyNames,
                  'countiesReturned' => $counties
                ), 500);
            }
            $city->counties()->sync($counties);
          }

        }
        return response()->json(null, 201);
    }

    public function states(Request $request)
    {
        // protected $fillable = ['id', 'title','slug','abbreviation'];
        $this->validate($request, [
          '*.state_fips' => 'required',
          '*.state_abr' => 'required',
          '*.state_name' => 'required'
         ]);
        $requests = $request->all();

        forEach($requests as $_request) {
          $state = new State();
          $state->id = $_request['state_fips'];
          $state->title = $_request['state_name'];
          $state->slug = str_slug($_request['state_name']);
          $state->abbreviation = $_request['state_abr'];
          $state->save();
        }
        return response()->json(null, 201);
    }

    public function data(Request $request)
    {
        $requests = $request->all();

        $this->validate($request, [
          '*.id' => 'required',
          '*.year' => 'required',
          '*.month' => 'required',
          '*.population_est' => 'required',
          '*.crime_count' => 'required',
          '*.source_id' => 'required',
          '*.crime_type' => 'required'
         ]);
        $requests = $request->all();

        forEach($requests as $_request) {


          $data = new Data();
          
          $month = $_request['month'];
          // print_r($month);
          $datatype = (int) $month === 0 ? 1 : 2;  // 1 = yearly, 2 = monthly
          // print_r($datatype);
          $data->datatype = $datatype;

          $month = $month === '0' ? '1' : $month;

          $year = $_request['year'];
          $date = date ('Y-m-d', strtotime($month . '/1/' . $year . ' 00:00:00') );

          $data->date = $date;

          $data->city_id = $_request['id'];
          $data->source_id = $_request['source_id'];
          $data->crime_id = $_request['crime_type'];

          $crimeCount =  $_request['crime_count'];
          $data->crimeCount = $crimeCount;

          $pop = $_request['population_est'];
          $data->population = $pop;

          // $per100 = round($pop / 100000, 2);
          $per100 = round( ($datatype === 1 ? 1 : 12) * $crimeCount / $pop * 100000, 2 );
          $data->per100k = $per100;

          $data->save();
        }
        return response()->json(null, 201);
    }


    public function instances(Request $request)
    {
        $requests = $request->all();

        $this->validate($request, [
          '*.year' => 'required',
          '*.month' => 'required',
          '*.date' => 'required',
          '*.state_abr' => 'required',
          '*.crime_type' => 'required',
          '*.crime_count' => 'required',
          '*.latitude' => 'required',
          '*.longitude' => 'required',
          '*.geoid' => 'required',
          '*.population' => 'required'
         ]);
        $requests = $request->all();

        forEach($requests as $_request) {


          $instance = new Instance();

          $instance->year = $_request['year'];
          $instance->month = $_request['month'];

          $date = date ('Y-m-d', strtotime($_request['date'] . ' 00:00:00') );
          $instance->date = $date;

          $instance->state_abr = $_request['state_abr'];
          $instance->crime_type = $_request['crime_type'];
          $instance->crimeCount = $_request['crime_count'];
          $instance->long = $_request['longitude'];
          $instance->lat = $_request['latitude'];
          $instance->tract_id = $_request['geoid'];
          $instance->population = intval(str_replace(",", "", $_request['population']));

          $instance->save();
        }
        return response()->json(null, 201);
    }

    // /**
    //  * Get the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //   return response()->json(Data::with('state')->find($id));
    // }


    // *
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
     
    // public function update(Request $request, $id)
    // {

    //   $this->validate($request, [
    //   'title' => 'required',
    //   'slug' => 'required',
    //   'county' => 'required',
    //   'photo' => 'required',
    //   'populationGroup' => 'required',
    //   'state_id' => 'required',
    //   'body' => 'required'
    //    ]);

    //   $data = Data::find($id);
    //   $data->county = $request->input('county', $data->county);
    //   $data->slug = str_slug($request->input('slug', $data->slug));
    //   $data->photo = $request->input('photo', $data->photo);
    //   $data->populationGroup = $request->input('populationGroup', $data->populationGroup);
    //   $data->state_id = $request->input('state_id', $data->state_id);
    //   $data->title = $request->input('title', $data->title);
    //   $data->body = $request->input('body', $data->body);
    //   $data->save();
    //   return response()->json($data);

    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //   Data::find($id)->delete();
    //   return response('', 200);
    // }
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request)
    // {
    //   // change this
    //   $maxCount = Data::count();
    //   $maxLimit = 1000;

    //   $offset = $request->input('offset') ? (int) $request->input('offset') : 0;
    //   $offset = $offset < 0 ? 0 : $offset;
    //   $offset = $offset > $maxCount ? $maxCount : $offset;

    //   $limit = $request->input('limit') ? (int) $request->input('limit') : 10;
    //   $limit = $limit < 1 ? 1 : $limit;
    //   $limit = $limit > $maxLimit ? $maxLimit : $limit;

    //   $order = $request->input('order') ? $request->input('order') : 0;
    //   $order = $order == "ASC" ? "ASC" : "DESC";

    //   $city_id = $request->input('city_id') ? explode(',', $request->input('city_id')) : false;
    //   $city_slug = $request->input('city_slug') ? explode(',', $request->input('city_slug')) : false;

    //   $crime_id = $request->input('crime_id') ? explode(',', $request->input('crime_id')) : false;
    //   $crime_slug = $request->input('crime_slug') ? explode(',', $request->input('crime_slug')) : false;

    //   $data = Data::with(['city' => function ($query) use ($city_id, $city_slug, $crime_id, $crime_slug) {
    //     if ($city_slug) {
    //       $query->where('slug', 'LIKE', $city_slug);
    //     }
    //   }, 'city.state', 'crime'])->take($limit)->skip($offset)->get();

    //   return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
    // }

}