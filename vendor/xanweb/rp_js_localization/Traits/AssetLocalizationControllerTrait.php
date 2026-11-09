<?php

namespace Xanweb\RpJsLocalization\Traits;

use Concrete\Core\Http\ResponseFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Xanweb\RpJsLocalization\AssetLocalizationCollection;

trait AssetLocalizationControllerTrait
{
    private AssetLocalizationCollection $assetLocalization;

    public function on_start()
    {
        $assetLocalization = new AssetLocalizationCollection();

        $this->assetLocalization = $this->dispatchEvent($assetLocalization);
    }

    private function createJavascriptResponse(string $content): Response
    {
        $rf = $this->app->make(ResponseFactoryInterface::class);

        return $rf->create(
            $content,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/javascript; charset=' . APP_CHARSET,
                'Content-Length' => strlen($content),
            ]
        );
    }
}
