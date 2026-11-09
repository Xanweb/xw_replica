<?php

namespace Xanweb\RpJsLocalization\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Xanweb\RpJsLocalization\AssetLocalizationCollection;

class FrontendAssetLocalizationLoad extends Event
{
    /**
     * Event Name
     *
     * @var string
     */
    public const NAME = 'rp_on_frontend_asset_localization_load';

    private AssetLocalizationCollection $assetLocalization;

    public function __construct(AssetLocalizationCollection $assetLocalization)
    {
        $this->assetLocalization = $assetLocalization;
    }

    /**
     * @return AssetLocalizationCollection
     */
    public function getAssetLocalization(): AssetLocalizationCollection
    {
        return $this->assetLocalization;
    }
}
