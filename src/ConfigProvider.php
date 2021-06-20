<?php

declare(strict_types=1);

namespace Trrtly\Casbin;

use Trrtly\Casbin\Contract\RuleInterface;
use Trrtly\Casbin\Repository\RuleRepository;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                RuleInterface::class => RuleRepository::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'commands' => [
                Command\CacheClear::class,
                Command\GroupAdd::class,
                Command\PolicyAdd::class,
                Command\RoleAssign::class,
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for casbin.',
                    'source' => __DIR__ . '/../publish/casbin.php',
                    'destination' => BASE_PATH . '/config/autoload/casbin.php',
                ],
                [
                    'id' => 'model',
                    'description' => 'RBAC Model file',
                    'source' => __DIR__ . '/../publish/casbin-rbac-model.conf',
                    'destination' => BASE_PATH . '/config/autoload/casbin-rbac-model.conf',
                ],
                [
                    'id' => 'database',
                    'description' => 'The database for casbin.',
                    'source' => __DIR__ . '/../database/migrations/2021_03_15_180604_create_rules_table.php',
                    'destination' => BASE_PATH . '/migrations/2021_03_15_180604_create_rules_table.php',
                ],
            ],
        ];
    }
}
