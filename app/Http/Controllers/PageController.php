<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json(Page::all());
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
        'body' => 'required'
         ]);

        $page = new Page();

        $page->slug = $request->input('slug');
        $page->title = $request->input('title');
        $page->body = $request->input('body');

        $page->save();
        return response()->json($page, 201);
    }

    /**
     * Get the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      return response()->json(Page::find($id));
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
      'body' => 'required'
       ]);

      $page = Page::find($id);
      $page->slug = $request->input('slug', $page->slug);
      $page->title = $request->input('title', $page->title);
      $page->body = $request->input('body', $page->body);
      $page->save();
      return response()->json($page);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Page::find($id)->delete();
      return response('', 200);
    }

}