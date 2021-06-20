<?php

declare(strict_types=1);

namespace Trrtly\Casbin\Facade;

use Hyperf\Utils\ApplicationContext;

abstract class Facade
{
    public static function __callStatic($method, $args)
    {
        return ApplicationContext::getContainer()->get(static::getFacadeRoot())->{$method}(...$args);
    }

    abstract public static function getFacadeRoot(): string;
}
