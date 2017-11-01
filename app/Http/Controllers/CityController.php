<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json(City::with('state')->get());
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
        'slug' => 'required',
        'photo' => 'required',
        'state_id' => 'required',
        'body' => 'required'
         ]);

        $city = new City();

        $city->slug = $request->input('slug');
        $city->title = $request->input('title');
        $city->photo = $request->input('photo', $city->photo);
        $city->state_id = $request->input('state_id', $city->state_id);
        $city->body = $request->input('body');

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
      'photo' => 'required',
      'state_id' => 'required',
      'body' => 'required'
       ]);

      $city = City::find($id);
      $city->slug = $request->input('slug', $city->slug);
      $city->photo = $request->input('photo', $city->photo);
      $city->state_id = $request->input('state_id', $city->state_id);
      $city->title = $request->input('title', $city->title);
      $city->body = $request->input('body', $city->body);
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