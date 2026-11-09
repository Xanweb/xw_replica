<?php

namespace Xanweb\RpRequest;

use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\User\UserInfo;
use Xanweb\RpCommon\Traits\ApplicationTrait;
use Xanweb\RpCommon\Traits\SingletonTrait;
use Xanweb\RpRequest\Traits\AttributesTrait;

/**
 * Class User
 *
 * @method static bool isRegistered()
 * @method static bool isSuperUser()
 * @method static int getUserID()
 * @method static string getUserName()
 * @method static string getUserEmail()
 */
class User
{
    use ApplicationTrait;
    use AttributesTrait;
    use SingletonTrait;

    protected array $cache = [];
    protected ConcreteUser $user;
    private UserInfo $ui;

    public function __construct()
    {
        $this->user = $this->app(ConcreteUser::class);
    }

    public static function canAccessDashboard(): bool
    {
        $ru = self::get();

        return $ru->cache['canAccessDashboard'] ??= ($ru->user->isRegistered() && $ru->app('helper/concrete/dashboard')->canRead());
    }

    public static function getUserInfoObject(): ?UserInfo
    {
        $ru = self::get();
        if (!$ru->user->isRegistered()) {
            return null;
        }

        return $ru->ui ??= $ru->user->getUserInfoObject();
    }

    public static function getAttribute($ak, $mode = false)
    {
        $ui = self::getUserInfoObject();
        if ($ui === null) {
            return null;
        }

        return self::_getAttribute($ui, $ak, $mode);
    }

    public function __call($name, $arguments)
    {
        return $this->user->$name(...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        $ru = self::get();
        if (method_exists($ru->user, $name)) {
            return $ru->user->$name(...$arguments);
        }

        $ui = self::getUserInfoObject();
        if ($ui === null) {
            return null;
        }

        if (method_exists($ui, $name)) {
            return $ui->$name(...$arguments);
        }

        throw new \LogicException(t('Cannot call non existing method %s->%s.', static::class, $name));
    }
}