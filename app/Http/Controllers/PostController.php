<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      return response()->json(Post::all());
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
        'author' => 'required',
        // 'photo' => 'required',
        'published' => 'required',
        'date' => 'required'
         ]);

        $post = new Post();

        $post->slug = str_slug($request->input('slug'));
        $post->title = $request->input('title');
        $post->body = $request->input('body');

        $post->author = $request->input('author');
        $post->photo = $request->input('photo');
        $post->published = $request->input('published');
        $post->date = $request->input('date');

        $post->save();
        return response()->json($post, 201);
    }

    /**
     * Get the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      $post = Post::find($id);
      if (empty($post)) {
        $post = Post::where('slug', $id)->firstOrFail();
      }
      return response()->json($post);
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
        'author' => 'required',
        // 'photo' => 'required',
        'published' => 'required',
        'date' => 'required'
         ]);

      $post = Post::find($id);
      $post->slug = str_slug($request->input('slug', $post->slug));
      $post->title = $request->input('title', $post->title);
      $post->body = $request->input('body', $post->body);

      $post->author = $request->input('author', $post->author);
      $post->photo = $request->input('photo', $post->photo);
      $post->published = $request->input('published', $post->published);
      $post->date = $request->input('date', $post->date);

      $post->save();
      return response()->json($post);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
      Post::find($id)->delete();
      return response('', 200);
    }

}