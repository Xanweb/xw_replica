<?php

namespace Xanweb\RpCommon\Traits;

use Concrete\Core\Support\Facade\Application;

trait StaticApplicationTrait
{
    /**
     * @param string $make [optional]
     *
     * @return \Concrete\Core\Application\Application|mixed
     */
    protected static function app($make = null)
    {
        $app = Application::getFacadeApplication();

        return ($make !== null) ? $app->make($make) : $app;
    }
}
