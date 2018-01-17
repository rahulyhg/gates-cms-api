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
use App\Controllers\PageController;


$router->get('/', function () use ($router) {
    return $router->app->version();
});



$router->group(['prefix' => 'api/v1/cities/'], function ($app) {
  $app->get('/','CityController@api'); //get all the cities  
});

$router->group(['prefix' => 'api/v1/timespan/'], function ($app) {
  $app->get('/','CityController@timespan'); //get all the timespan  
});



$router->group(['prefix' => 'api/v1/crimes/'], function ($app) {
  $app->post('/','CityController@crimes');
  $app->get('/','CityController@crimes');
});





$router->group(['prefix' => 'api/v1/upload/', 'middleware' => 'auth'], function ($app) {
  $app->post('/','MediaController@index');  
});

$router->group(['prefix' => 'api/v1/page/', 'middleware' => 'auth'], function ($app) {
  $app->post('/','PageController@store'); //store single route
  $app->patch('/{id}/','PageController@update'); //update single route
  $app->delete('/{id}/','PageController@destroy'); //delete single route
});

$router->group(['prefix' => 'api/v1/page/'], function ($app) {
  $app->get('/','PageController@index'); //get all the routes  
  $app->get('/{id}/', 'PageController@show'); //get single route
});

$router->group(['prefix' => 'api/v1/city/', 'middleware' => 'auth'], function ($app) {
  $app->post('/','CityController@store'); //store single route
  $app->patch('/{id}/','CityController@update'); //update single route
  $app->delete('/{id}/','CityController@destroy'); //delete single route
});

$router->group(['prefix' => 'api/v1/city/'], function ($app) {
  $app->get('/','CityController@index'); //get all the routes  
  $app->get('/{id}/', 'CityController@show'); //get single route
});


$router->group(['prefix' => 'api/v1/member/', 'middleware' => 'auth'], function ($app) {
  $app->post('/','MemberController@store'); //store single route
  $app->patch('/{id}/','MemberController@update'); //update single route
  $app->delete('/{id}/','MemberController@destroy'); //delete single route
});

$router->group(['prefix' => 'api/v1/member/'], function ($app) {
  $app->get('/','MemberController@index'); //get all the routes  
  $app->get('/{id}/', 'MemberController@show'); //get single route
});

$router->group(['prefix' => 'api/v1/state/', 'middleware' => 'auth'], function ($app) {
  $app->post('/','StateController@store'); //store single route
  $app->patch('/{id}/','StateController@update'); //update single route
  $app->delete('/{id}/','StateController@destroy'); //delete single route
});
$router->group(['prefix' => 'api/v1/state/'], function ($app) {
  $app->get('/','StateController@index'); //get all the routes  
  $app->get('/{id}/', 'StateController@show'); //get single route
});

$router->group(['prefix' => 'api/v1/sheet/'], function ($app) {
  $app->get('/','SheetController@index'); //get all the routes  
  $app->post('/','SheetController@store'); //get newest sheet
  $app->get('/view/{id}/', 'SheetController@show'); //get single route
  $app->delete('/{id}/','SheetController@destroy'); //delete single route
});

$router->group(['prefix' => 'api/v1/import/', 'middleware' => 'auth'], function ($app) {  
  $app->post('/dump','DataController@dump'); //get newest sheet
  $app->post('/data','DataController@data'); //get newest sheet
  $app->post('/states','DataController@states'); //get newest sheet
  $app->post('/cities','DataController@cities'); //get newest sheet
  $app->post('/crimes','DataController@crimes'); //get newest sheet
  $app->post('/sources','DataController@sources'); //get newest sheet
});