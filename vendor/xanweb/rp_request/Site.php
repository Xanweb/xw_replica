<?php

namespace Xanweb\RpRequest;

use Concrete\Core\Entity\Site\Site as ConcreteSite;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Support\Facade\Url;
use League\URL\URLInterface;
use Xanweb\RpCommon\Traits\ApplicationTrait;
use Xanweb\RpCommon\Traits\SingletonTrait;
use Xanweb\RpRequest\Page as RequestPage;
use Xanweb\RpRequest\Traits\AttributesTrait;

class Site
{
    use ApplicationTrait;
    use AttributesTrait;
    use SingletonTrait;

    protected array $cache = [];
    protected ?ConcreteSite $site;

    public function __construct()
    {
        $this->site = $this->app('site')->getSite();
    }

    public static function urlToDefaultLocaleHome(): ?URLInterface
    {
        $rs = self::get();

        return $rs->cache['urlToDefaultLocaleHome'] ??= Url::to(static::getSiteHomePageObject());
    }

    /**
     * @deprecated Use urlToDefaultLocaleHome()
     */
    public static function getSiteHomePageURL(): ?URLInterface
    {
        return self::urlToDefaultLocaleHome();
    }

    public static function getSiteHomePageObject(): ?ConcretePage
    {
        $rs = self::get();
        if (!isset($rs->cache['siteHomePageObject']) && $homePageID = static::getSiteHomePageID()) {
            $homePage = ConcretePage::getByID($homePageID, 'ACTIVE');
            if (is_object($homePage) && !$homePage->isError()) {
                $rs->cache['siteHomePageObject'] = $homePage;
            }
        }

        return $rs->cache['siteHomePageObject'];
    }

    public static function getSiteHomePageID(): int
    {
        $rs = self::get();

        return $rs->cache['siteHomePageID'] ??= (int) $rs->site->getSiteHomePageID();
    }

    public static function urlToHome(): URLInterface
    {
        $rs = self::get();

        return $rs->cache['urlToHome'] ??= Url::to(static::getLocaleHomePageObject() ?? '');
    }

    /**
     * @deprecated Use urlToHome()
     */
    public static function getLocaleHomePageURL(): ?URLInterface
    {
        return self::urlToHome();
    }

    public static function getLocaleHomePageObject(): ?ConcretePage
    {
        $rs = self::get();
        if (!isset($rs->cache['localeHomePageObject']) && $homePageID = static::getLocaleHomePageID()) {
            $homePage = ConcretePage::getByID($homePageID, 'ACTIVE');
            if (is_object($homePage) && !$homePage->isError()) {
                $rs->cache['localeHomePageObject'] = $homePage;
            }
        }

        return $rs->cache['localeHomePageObject'];
    }

    public static function getLocaleHomePageID(): int
    {
        $rs = self::get();
        if (!isset($rs->cache['localeHomePageID'])) {
            $localeHomePageID = 0;
            $activeLocale = RequestPage::getLocale();
            foreach ($rs->site->getLocales() as $locale) {
                if ($locale->getLocale() === $activeLocale) {
                    $localeHomePageID = $locale->getSiteTreeObject()->getSiteHomePageID();
                    break;
                }
            }

            $rs->cache['localeHomePageID'] = (int) $localeHomePageID;
        }

        return $rs->cache['localeHomePageID'];
    }

    public static function getDisplaySiteName(): string
    {
        return tc('SiteName', static::getSiteName());
    }

    public static function getSiteName(): string
    {
        $rs = self::get();

        return $rs->cache['siteName'] ??= $rs->site->getSiteName();
    }

    public static function getAttribute($ak, $mode = false)
    {
        $rs = self::get();

        return ($rs->site !== null) ? self::_getAttribute($rs->site, $ak, $mode) : null;
    }

    public function __call($name, $arguments)
    {
        return $this->site->$name(...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return self::get()->site->$name(...$arguments);
    }
}
