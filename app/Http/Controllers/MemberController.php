<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json(Member::all());
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
        'body' => 'required',
        'photo' => 'required'
         ]);

        $member = new Member();

        $member->slug = $request->input('slug');
        $member->title = $request->input('title');
        $member->body = $request->input('body');
        $member->photo = $request->input('photo');

        $member->save();
        return response()->json($member, 201);
    }

    /**
     * Get the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return response()->json(Member::find($id));
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
      'body' => 'required',
      'photo' => 'required'
       ]);

      $member = Member::find($id);
      $member->slug = $request->input('slug', $member->slug);
      $member->title = $request->input('title', $member->title);
      $member->body = $request->input('body', $member->body);
      $member->photo = $request->input('photo', $member->photo);
      $member->save();
      return response()->json($member);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Member::find($id)->delete();
      return response('', 200);
    }

}