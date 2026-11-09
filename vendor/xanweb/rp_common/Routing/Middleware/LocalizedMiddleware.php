<?php

namespace Xanweb\RpCommon\Routing\Middleware;

use Concrete\Core\Http\Middleware\DelegateInterface;
use Concrete\Core\Http\Middleware\MiddlewareInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Site\Service as SiteService;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Class LocalizedMiddleware
 */
class LocalizedMiddleware implements MiddlewareInterface
{
    protected SiteService $siteService;
    protected Localization $localization;

    public function __construct(SiteService $siteService, Localization $localization)
    {
        $this->siteService = $siteService;
        $this->localization = $localization;
    }

    /**
     * {@inheritdoc}
     *
     * @see MiddlewareInterface::process()
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $localeArgMetadata = new ArgumentMetadata('_locale', 'string', false, false, $this->localization->getLocale());
        $resolver = new RequestAttributeValueResolver();
        if ($resolver->supports($request, $localeArgMetadata)) {
            $resolved = $resolver->resolve($request, $localeArgMetadata);
            if (!$resolved instanceof \Generator) {
                $this->throwInvalidLocaleException();
            }

            $_locale = null;
            foreach ($resolved as $append) {
                $_locale = $append;
                break;
            }

            $_locale = explode('_', str_replace('-', '_', $_locale));
            if (!$_locale || $_locale === []) {
                $this->throwInvalidLocaleException();
            }

            $activeLocale = null;
            $uiLocale = $this->localization->getContextLocale(Localization::CONTEXT_UI);
            if (strcasecmp($uiLocale, implode('_', $_locale)) === 0) {
                $activeLocale = $uiLocale;
            } else {
                $site = $this->siteService->getSite();
                foreach ($site->getLocales() as $locale) {
                    if ($locale->getLanguage() === $_locale[0] && (!isset($_locale[1]) || $locale->getCountry() === Str::upper($_locale[1]))) {
                        $activeLocale = $locale->getLocale();
                        break;
                    }
                }
            }

            if (!$activeLocale) {
                $this->throwInvalidLocaleException();
            }

            $this->localization->setContextLocale(Localization::CONTEXT_SITE, $activeLocale);
            $this->localization->setActiveContext(Localization::CONTEXT_SITE);
        }

        return $frame->next($request);
    }

    /**
     * @throw InvalidArgumentException
     */
    private function throwInvalidLocaleException(): void
    {
        throw new \InvalidArgumentException(t('Invalid locale provided for url.'));
    }
}
