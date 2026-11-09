<?php

namespace Xanweb\RpCommon\Traits;

trait SingletonTrait
{
    private static self $instance;

    /**
     * Gets a singleton instance of this class.
     *
     * @return static
     */
    public static function get(): self
    {
        return self::$instance ??= new static();
    }
}
