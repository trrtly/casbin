<?php

declare(strict_types=1);

namespace Trrtly\Casbin\Facade;

use Trrtly\Casbin\EnforcerManager;

/**
 * @mixin \Casbin\Enforcer
 */
class Enforcer extends Facade
{
    public static function getFacadeRoot(): string
    {
        return EnforcerManager::class;
    }
}
