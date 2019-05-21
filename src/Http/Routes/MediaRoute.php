<?php

namespace Knovators\Media\Http\Routes;


use Knovators\Support\Routing\RouteRegistrar;

/**
 * Class MediaRoute
 *
 * @package  Knovators\Media\Http\Routes
 *
 * @codeCoverageIgnore
 */
class MediaRoute extends RouteRegistrar
{

    /**
     * Map all routes.
     */
    public function map() {
        $this->group($this->adminAttributes(), function () {
            $this->name('media.')->group(function () {
                $this->name('index')->get('list', 'MediaController@index');
            });
        });

        $this->group($this->clientAttributes(), function () {
            $this->name('media.')->group(function () {
                $this->name('upload')->post('upload', 'MediaController@store');
                $this->name('user.index')->get('user/list', 'MediaController@userImages');
            });
        });

    }


    /**
     * @return mixed
     */
    public function adminAttributes() {
        return $this->config('admin_attributes', []);
    }


    /**
     * @return mixed
     */
    public function clientAttributes() {
        return $this->config('client_attributes', []);
    }


    /**
     * Get config value by key
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    private function config($key, $default = null) {
        return config("media.route.$key", $default);
    }


}
