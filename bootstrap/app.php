<?php

require_once __DIR__.'/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__.'/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    realpath(__DIR__.'/../')
);
// $app->register('Nord\Lumen\Cors\CorsServiceProvider');

$app->withFacades();

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton('zipper', function ($app) {
    return $app->loadComponent(
        'zipper',
        Chumper\Zipper\ZipperServiceProvider::class,
        'zipper'
    );
});

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/
$app->middleware([
    App\Http\Middleware\CorsMiddleware::class
    // 'Nord\Lumen\Cors\CorsMiddleware',
    // \Barryvdh\Cors\HandleCors::class
]);

// $app->middleware([
//    App\Http\Middleware\ExampleMiddleware::class
// ]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/
// $app->configure('cors');
// $app->register(Barryvdh\Cors\ServiceProvider::class);

// $app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
// $app->register(App\Providers\EventServiceProvider::class);

$app->register('Maatwebsite\Excel\ExcelServiceProvider');
class_alias('Maatwebsite\Excel\Facades\Excel', 'Excel');
class_alias('Illuminate\Support\Facades\Response', 'Response');
class_alias('Illuminate\Support\Facades\Config', 'Config');

// $app->register(Chumper\Zipper\ZipperServiceProvider::class);
class_alias(Chumper\Zipper\Zipper::class, 'Zipper');

$app->register(Grimzy\LaravelMysqlSpatial\SpatialServiceProvider::class);


// class_alias('ZanySoft\Zip\Zip', 'Zip');

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__.'/../routes/web.php';
});

return $app;
