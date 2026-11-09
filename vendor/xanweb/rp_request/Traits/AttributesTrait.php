<?php

namespace Xanweb\RpRequest\Traits;

/**
 * @internal
 */
trait AttributesTrait
{
    private static function _getAttribute(object $obj, $ak, $mode = false)
    {
        $cacheKey = 'ak_' . (is_object($ak) ? $ak->getAttributeKeyHandle() : $ak);
        $modeKey = $mode ? "_$mode" : '_';

        $inst = self::get();
        if (!isset($inst->cache[$cacheKey]) || !array_key_exists($modeKey, $inst->cache[$cacheKey])) {
            $inst->cache[$cacheKey] ??= [];
            $inst->cache[$cacheKey][$modeKey] = $obj->getAttribute($ak, $mode);
        }

        return $inst->cache[$cacheKey][$modeKey];
    }
}
