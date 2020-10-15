<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Phinx\Migration\AbstractMigration;
use Ravisdo\Bot\Configuration;

include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$environmentConfiguration = Dotenv::createImmutable(__DIR__ . DIRECTORY_SEPARATOR . '..')->safeLoad();
$configuration = Configuration::fromArray($environmentConfiguration);

return [
    'paths' => [
        'migrations'    => '%%PHINX_CONFIG_DIR%%/../database/migrations',
        'seeds'         => '%%PHINX_CONFIG_DIR%%/../database/seeds',
    ],
    'templates' => [
        'file' => 'phinx-template.php.dist'
    ],
    'migration_base_class' => AbstractMigration::class,
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'default',
        'default' => [
            'adapter' => 'mysql',
            'host' => $configuration->getMySqlHostname(),
            'name' => $configuration->getMySqlDatabaseName(),
            'user' => $configuration->getMySqlUsername(),
            'pass' => $configuration->getMySqlPassword(),
            'port' => 3306
        ],
    ],
];
