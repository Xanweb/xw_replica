<?php

namespace Xanweb\Replica\Module;

use Xanweb\RpHtmlHelper\ServiceProvider as HtmlServiceProvider;
use Xanweb\RpModule\Module as AbstractModule;
use Xanweb\RpVueItemList\ServiceProvider as VItemListServiceProvider;

class Module extends AbstractModule
{
    public static function pkgHandle(): string
    {
        return 'xw_replica';
    }

    public static function themeHandle(): string
    {
        return 'xw_replica';
    }

    /**
     * {@inheritdoc}
     *
     * @see AbstractModule::getServiceProviders()
     */
    protected static function getServiceProviders(): array
    {
        return [
            VItemListServiceProvider::class,
            HtmlServiceProvider::class,
        ];
    }

}
