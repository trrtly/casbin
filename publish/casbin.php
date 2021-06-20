<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    // Casbin adapter.
    'adapter' => \Trrtly\Casbin\Adapter\DatabaseAdapter::class,

    // Database setting.
    'database' => [
        // Database connection for following tables.
        'connection' => '',

        // Rule table name.
        'rules_table' => 'rules',
    ],

    // Cache setting.
    'cache' => [
        'enabled' => true,
        'key' => 'casbin',
        'ttl' => 86400,
    ],

    'log' => [
        // changes whether casbin will log messages to the Logger.
        'enabled' => false,
    ],

    // Casbin model setting.
    'model' => [
        // Available Settings: "file", "text".
        'config_type' => 'text',

        'config_text' => <<<'EOT'
[request_definition]
r = sub, obj, act

[policy_definition]
p = sub, obj, act

[role_definition]
g = _, _

[policy_effect]
e = some(where (p.eft == allow))

[matchers]
m = g(r.sub, p.sub) && r.obj == p.obj && r.act == p.act
EOT,
        // when config_type is "file".
        'config_file_path' => __DIR__ . '/casbin-rbac-model.conf',
    ],
];
