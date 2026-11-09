<?php

namespace Xanweb\RpJsLocalization\Event;

use Symfony\Contracts\EventDispatcher\Event;
use Xanweb\RpJsLocalization\AssetLocalizationCollection;

class BackendAssetLocalizationLoad extends Event
{
    /**
     * Event Name
     *
     * @var string
     */
    public const NAME = 'rp_on_backend_asset_localization_load';

    private AssetLocalizationCollection $assetLocalization;

    public function __construct(AssetLocalizationCollection $assetLocalization)
    {
        $this->assetLocalization = $assetLocalization;
    }

    public function getAssetLocalization(): AssetLocalizationCollection
    {
        return $this->assetLocalization;
    }
}
