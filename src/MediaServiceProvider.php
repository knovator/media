<?php

namespace Knovators\Media;

use Knovators\Support\PackageServiceProvider;

/**
 * Class MediaServiceProvider
 * @package Knovators\Media
 */
class MediaServiceProvider extends PackageServiceProvider
{
    /* -----------------------------------------------------------------
    |  Properties
    | -----------------------------------------------------------------
    */

    /**
     * Package name.
     *
     * @var string
     */
    protected $package = 'media';

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Register the service provider.
     */
    public function register()
    {
        parent::register();

        $this->registerConfig();

        $this->registerProviders([
            Providers\RouteServiceProvider::class,
        ]);
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        parent::boot();
        $this->publishConfig();
        $this->loadMigrations();
        $this->publishTranslations();
    }


}
