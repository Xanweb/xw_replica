<?php

namespace Xanweb\RpJsLocalization\Controller\Frontend;

use Concrete\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Xanweb\RpJsLocalization\AssetLocalizationCollection;
use Xanweb\RpJsLocalization\Event\FrontendAssetLocalizationLoad;
use Xanweb\RpJsLocalization\Traits\AssetLocalizationControllerTrait;

class AssetLocalization extends Controller
{
    use AssetLocalizationControllerTrait;

    public function dispatchEvent(AssetLocalizationCollection $assetLocalization): AssetLocalizationCollection
    {
        $this->app['director']->dispatch(
            FrontendAssetLocalizationLoad::NAME,
            $event = new FrontendAssetLocalizationLoad($assetLocalization)
        );

        return $event->getAssetLocalization();
    }

    public function getJavascript(): Response
    {
        $content = 'window.xw_frontend=' . $this->assetLocalization->toJson() . ';';

        return $this->createJavascriptResponse($content);
    }
}
