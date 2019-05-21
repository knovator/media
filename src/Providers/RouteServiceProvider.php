<?php

namespace Knovators\Media\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Knovators\Media\Http\Routes\MediaRoute;

/**
 * Class  RouteServiceProvider
 * @package  Knovators\Media\Providers
 */
class RouteServiceProvider extends ServiceProvider
{

    protected $namespace = 'Knovators\\Media\\Http\\Controllers';


    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Define the routes for the application.
     */
    public function map() {
        Route::namespace($this->namespace)
             ->group(function () {
                 MediaRoute::register();
             });
    }


}
