<?php

namespace Xanweb\RpHtmlHelper;

use Xanweb\RpCommon\Service\Provider as FoundationServiceProvider;
use Xanweb\RpHtmlHelper\Service\Form;

class ServiceProvider extends FoundationServiceProvider
{
    public function _register(): void
    {
        $this->app->bind(\Concrete\Core\Form\Service\Form::class, Form::class);
        $this->app->singleton('helper/form', Form::class);
        $this->app->singleton('helper/form/ios_toggler', fn ($app) => new Service\Form\IosTogglerWidget($app['helper/form']));
    }
}
