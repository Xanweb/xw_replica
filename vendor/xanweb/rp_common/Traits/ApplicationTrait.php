<?php

namespace Xanweb\RpCommon\Traits;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Facade\Application as FacadeApp;

trait ApplicationTrait
{
    protected Application $app;

    /**
     * @param string $make [optional]
     *
     * @return Application|mixed
     * @noinspection PhpDocMissingThrowsInspection
     */
    protected function app($make = null)
    {
        $this->app ??= FacadeApp::getFacadeApplication();

        return ($make !== null) ? $this->app->make($make) : $this->app;
    }
}
