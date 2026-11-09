<?php

namespace Xanweb\RpRequest;

use Concrete\Core\Foundation\ClassAliasList;
use Xanweb\RpCommon\Service\Provider as FoundationProvider;

class ServiceProvider extends FoundationProvider
{
    protected function _register(): void
    {
        ClassAliasList::getInstance()->registerMultiple([
            'RequestUser' => User::class,
            'RequestPage' => Page::class,
            'RequestSite' => Site::class,
        ]);
    }
}
