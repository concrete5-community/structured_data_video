<?php

namespace Concrete\Package\StructuredDataVideo;

use A3020\StructuredDataVideo\Provider;
use Concrete\Core\Package\Package;

/**
 * @copyright A3020
 */
final class Controller extends Package
{
    protected $pkgHandle = 'structured_data_video';
    protected $appVersionRequired = '8.4.1';
    protected $pkgVersion = '1.1.1';
    protected $pkgAutoloaderRegistries = [
        'src/StructuredDataVideo' => '\A3020\StructuredDataVideo',
    ];

    public function getPackageName()
    {
        return t('Structured Data Video');
    }

    public function getPackageDescription()
    {
        return t('Add structured data to your videos.');
    }

    public function on_start()
    {
        /** @var Provider $provider */
        $provider = $this->app->make(Provider::class);
        $provider->register();
    }
}
