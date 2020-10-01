<?php

namespace Knovators\Media\Http\Routes;


use Knovators\Support\Routing\RouteRegister;

/**
 * Class MediaRoute
 * @package  Knovators\Media\Http\Routes
 */
class MediaRoute extends RouteRegister
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
            $this->post('upload', 'MediaController@store');
            $this->get('user/list', 'MediaController@userImages');

        });

    }


    /**
     * @return mixed
     */
    public function adminAttributes() {
        return $this->config('route.admin_attributes', []);
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
        return config("media.$key", $default);
    }

    /**
     * @return mixed
     */
    public function clientAttributes() {
        return $this->config('route.client_attributes', []);
    }


}
