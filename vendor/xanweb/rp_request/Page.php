<?php

namespace Xanweb\RpRequest;

use Concrete\Core\Http\Request;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Page\Page as ConcretePage;
use Concrete\Core\Support\Facade\Url;
use League\Url\UrlInterface;
use Xanweb\Common\Traits\ApplicationTrait;
use Xanweb\Common\Traits\SingletonTrait;
use Xanweb\RpRequest\Traits\AttributesTrait;

/**
 * @method static string getCollectionName()
 * @method static string getCollectionDescription()
 * @method static string getCollectionPath()
 * @method static string getCollectionDatePublic()
 * @method static \DateTime|null getCollectionDatePublicObject()
 * @method static string getCollectionDateLastModified()
 */
class Page
{
    use ApplicationTrait;
    use AttributesTrait;
    use SingletonTrait;

    private Request $request;
    protected array $cache = [];

    public function __construct()
    {
        $this->request = $this->app(Request::class);
    }

    public function url(): ?URLInterface
    {
        $c = self::getConcretePage();
        if ($c !== null) {
            return self::get()->cache['url'] ??= Url::to($c);
        }

        return null;
    }

    public static function getLocale(): string
    {
        $rp = self::get();
        if (!isset($rp->cache['locale'])) {
            $section = Section::getCurrentSection();
            $locale = is_object($section) ? $section->getLocale() : null;

            $rp->cache['locale'] = $locale ?? Localization::activeLocale();
        }

        return $rp->cache['locale'];
    }

    public static function getLanguage(): string
    {
        return \current(\explode('_', self::getLocale()));
    }

    public static function isEditMode(): bool
    {
        $rp = self::get();

        return $rp->cache['isEditMode'] ??= (($c = self::getConcretePage()) !== null && $c->isEditMode());
    }

    public static function getAttribute($ak, $mode = false)
    {
        $c = self::getConcretePage();

        return ($c !== null) ? self::_getAttribute($c, $ak, $mode) : null;
    }

    public function __call($name, $arguments)
    {
        $c = $this->request->getCurrentPage();
        if ($c !== null) {
            return $c->$name(...$arguments);
        }
    }

    public static function __callStatic($name, $arguments)
    {
        return self::get()->$name(...$arguments);
    }

    /**
     * Get Current Page Object.
     *
     * @return ConcretePage|null
     */
    protected static function getConcretePage(): ?ConcretePage
    {
        return self::get()->request->getCurrentPage();
    }
}
