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

$router->group(['prefix' => 'page/'], function ($app) {
    $app->get('/','PageController@index'); //get all the routes  
    $app->post('/','PageController@store'); //store single route
    $app->get('/{id}/', 'PageController@show'); //get single route
    $app->patch('/{id}/','PageController@update'); //update single route
    $app->delete('/{id}/','PageController@destroy'); //delete single route
});
