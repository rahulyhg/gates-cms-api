<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Http\Request;
use App\Models\Page;

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('page', function() {
  return response()->json(Page::all());
});

$router->get('page/{id}', function($id) {
  return response()->json(Page::find($id));
});

$router->post('page', function(Request $request) {
  $page = new Page();

  $page->title = $request->input('title');
  $page->body = $request->input('body');

  $page->save();
  return response()->json($page, 201);
});

$router->delete('page/{id}', function($id) {
  Page::find($id)->delete();
  return response('', 200);
});

$router->patch('page/{id}', function(Request $request, $id) {
  $page = Page::find($id);
  $page->title = $request->input('title', $page->title);
  $page->body = $request->input('body', $page->body);

  $page->save();
  return response()->json($page);
});