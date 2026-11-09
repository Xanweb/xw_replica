<?php
namespace Xanweb\RpHelpers;

use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Support\Facade\Application;
use Concrete\Theme\Concrete\PageTheme;

class Bootstrap
{
    public static function c5app(?string $abstract = null, array $parameters = [])
    {
        $app = Application::getFacadeApplication();

        if ($abstract === null) {
            return $app;
        }

        return $app->make($abstract, $parameters);
    }

    public static function strip_spaces(string $string): string
    {
        return Str::stripSpaces($string);
    }

    public static function remove_accents(string $string): string
    {
        return Str::removeAccents($string);
    }

    public static function absolute_path(string $relativePath): string
    {
        return Path::getAbsolutePath($relativePath);
    }

    public static function is_absolute_path(string $path): bool
    {
        return Path::isAbsolutePath($path);
    }

    public static function theme_path(): string
    {
        static $themePath;

        if (!$themePath) {
            $themePath = PageTheme::getSiteTheme()->getThemeURL();
        }

        return $themePath;
    }

    public static function active_language(): string
    {
        return Localization::activeLanguage();
    }

    public static function active_locale(): string
    {
        return Localization::activeLocale();
    }

    public static function current_language(): string
    {
        return \current(\explode('_', current_locale()));
    }

    public static function current_locale(): string
    {
        $section = Section::getCurrentSection();
        $locale = is_object($section) ? $section->getLocale() : null;

        return $locale ?? Localization::activeLocale();
    }

    public static function getRandomItemByInterval($timeBase, $array)
    {
        $randomIndexPos = (((int) $timeBase) % count($array));

        return $array[$randomIndexPos];
    }

    public static function c5_date_format_custom($format, $value = 'now', $toTimezone = 'user', $fromTimezone = 'system')
    {
        return self::c5app('date')->formatCustom($format, $value, $toTimezone, $fromTimezone);
    }

    public static function c5_date_format($value = 'now', $format = 'short', $toTimezone = 'user')
    {
        return self::c5app('date')->formatDate($value, $format, $toTimezone);
    }

    public static function in_array_all(array $needles, array $haystack): bool
    {
        return Arr::inArrayAll($needles, $haystack);
    }

    public static function in_array_any(array $needles, array $haystack): bool
    {
        return Arr::inArrayAny($needles, $haystack);
    }

    public static function str_starts_with(string $haystack, string $needle): bool
    {
        return 0 === strncmp($haystack, $needle, \strlen($needle));
    }
}
