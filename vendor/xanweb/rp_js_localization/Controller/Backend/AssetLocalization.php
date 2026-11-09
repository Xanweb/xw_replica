<?php

namespace Xanweb\RpJsLocalization\Controller\Backend;

use Concrete\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Xanweb\RpJsLocalization\AssetLocalizationCollection;
use Xanweb\RpJsLocalization\Event\BackendAssetLocalizationLoad;
use Xanweb\RpJsLocalization\Traits\AssetLocalizationControllerTrait;

class AssetLocalization extends Controller
{
    use AssetLocalizationControllerTrait;

    public function dispatchEvent(AssetLocalizationCollection $assetLocalization): AssetLocalizationCollection
    {
        $this->app['director']->dispatch(
            BackendAssetLocalizationLoad::NAME,
            $event = new BackendAssetLocalizationLoad($assetLocalization)
        );

        return $event->getAssetLocalization();
    }

    public function getJavascript(): Response
    {
        $content = 'window.xw_backend=' . $this->assetLocalization->toJson() . ';';

        return $this->createJavascriptResponse($content);
    }
}
