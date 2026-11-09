<?php

namespace Xanweb\RpCommon\Service;

use Concrete\Core\Foundation\Service\Provider as CoreServiceProvider;

abstract class Provider extends CoreServiceProvider
{
    private static array $registered = [];

    final public function register(): void
    {
        if ($this->isRegistered()) {
            return;
        }

        $this->_register();
        $this->markAsRegistered();
    }

    abstract protected function _register(): void;

    private function markAsRegistered(): void
    {
        self::$registered[static::class] = true;
    }

    public function isRegistered(): bool
    {
        return isset(self::$registered[static::class]);
    }
}
