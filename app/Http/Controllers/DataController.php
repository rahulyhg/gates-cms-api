<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Data;
class DataController extends Controller
{



  
 




  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      // change this
      $maxCount = Data::count();
      $maxLimit = 1000;

      $offset = $request->input('offset') ? (int) $request->input('offset') : 0;
      $offset = $offset < 0 ? 0 : $offset;
      $offset = $offset > $maxCount ? $maxCount : $offset;

      $limit = $request->input('limit') ? (int) $request->input('limit') : 10;
      $limit = $limit < 1 ? 1 : $limit;
      $limit = $limit > $maxLimit ? $maxLimit : $limit;

      $order = $request->input('order') ? $request->input('order') : 0;
      $order = $order == "ASC" ? "ASC" : "DESC";

      $city_id = $request->input('city_id') ? explode(',', $request->input('city_id')) : false;
      $city_slug = $request->input('city_slug') ? explode(',', $request->input('city_slug')) : false;

      $crime_id = $request->input('crime_id') ? explode(',', $request->input('crime_id')) : false;
      $crime_slug = $request->input('crime_slug') ? explode(',', $request->input('crime_slug')) : false;

      $data = Data::with(['city' => function ($query) use ($city_id, $city_slug, $crime_id, $crime_slug) {
        if ($city_slug) {
          $query->where('slug', 'LIKE', $city_slug);
        }
      }, 'city.state', 'crime'])->take($limit)->skip($offset)->get();

      return response()->json($data, 200, [], JSON_NUMERIC_CHECK);
    }

    public function refresh()
    {

    }


    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {

    //     $this->validate($request, [
    //     'title' => 'required',
    //     'county' => 'required',
    //     'slug' => 'required',
    //     'photo' => 'required',
    //     'populationGroup' => 'required',
    //     'state_id' => 'required',
    //     'body' => 'required'
    //      ]);

    //     $data = new Data();

    //     $data->slug = str_slug($request->input('slug'));
    //     $data->county = $request->input('county');
    //     $data->title = $request->input('title');
    //     $data->photo = $request->input('photo', $data->photo);
    //     $data->populationGroup = $request->input('populationGroup', $data->populationGroup);
    //     $data->state_id = $request->input('state_id', $data->state_id);
    //     $data->body = $request->input('body');

    //     $data->save();
    //     return response()->json($data, 201);
    // }

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

}