<?php

namespace Xanweb\RpJsLocalization;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Support\Facade\Route;
use Illuminate\Support\Str;
use Xanweb\RpJsLocalization\Controller\Backend\AssetLocalization as BackendAssetLocalization;
use Xanweb\RpJsLocalization\Controller\Frontend\AssetLocalization as FrontendAssetLocalization;
use Xanweb\RpCommon\Routing\Middleware\LocalizedMiddleware;
use Xanweb\RpCommon\Service\Provider as FoundationProvider;

class ServiceProvider extends FoundationProvider
{
    protected function _register(): void
    {
        $router = Route::getFacadeRoot();
        $router->get('/rp/xw/backend/js', BackendAssetLocalization::class . '::getJavascript');

        $router
            ->get('/xw/{_locale}/frontend/js', FrontendAssetLocalization::class . '::getJavascript')
            ->addMiddleware(LocalizedMiddleware::class);

        $this->registerAssets();
    }

    private function registerAssets(): void
    {
        $_locale = Str::lower(str_replace('_', '-', Localization::activeLocale()));

        $al = AssetList::getInstance();
        $al->register('javascript-localized', 'rp/xw/backend', '/rp/xw/backend/js', ['combine' => false, 'minify' => false]);
        $al->register('javascript-localized', 'xw/frontend', "/xw/$_locale/frontend/js", ['combine' => false, 'minify' => false]);
    }
}
