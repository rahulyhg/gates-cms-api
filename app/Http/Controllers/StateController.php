<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
class StateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json(State::all());
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
        'abbreviation' => 'required'
         ]);

        $state = new State();

        $state->slug = str_slug($request->input('slug'));
        $state->title = $request->input('title');
        $state->abbreviation = $request->input('abbreviation');

        $state->save();
        return response()->json($state, 201);
    }

    /**
     * Get the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return response()->json(State::with('cities')->find($id));
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
      'abbreviation' => 'required'
       ]);

      $state = State::find($id);
      $state->slug = str_slug($request->input('slug', $state->slug));
      $state->abbreviation = $request->input('abbreviation', $state->abbreviation);
      $state->title = $request->input('title', $state->title);
      $state->save();
      return response()->json($state);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      State::find($id)->delete();
      return response('', 200);
    }

}