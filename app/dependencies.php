<?php

declare(strict_types=1);

use DI\ContainerBuilder;

return function (ContainerBuilder $containerBuilder): void {
    $containerBuilder->addDefinitions([
        // Logger
        'logger' => function (): \Monolog\Logger {
            $logger = new \Monolog\Logger('whmcs');
            $logger->pushHandler(
                new \Monolog\Handler\StreamHandler(
                    APP_ROOT . '/var/logs/app.log',
                    \Monolog\Logger::DEBUG
                )
            );
            return $logger;
        },

        // Settings
        'settings' => [
            'displayErrorDetails' => $_ENV['APP_DEBUG'] === 'true',
            'logErrorDetails'     => true,
            'logErrors'           => true,
            'db'                  => [
                'host'    => $_ENV['DB_HOST'] ?? 'localhost',
                'name'    => $_ENV['DB_NAME'] ?? 'whmcs',
                'user'    => $_ENV['DB_USER'] ?? 'root',
                'pass'    => $_ENV['DB_PASS'] ?? '',
                'charset' => 'utf8mb4',
            ],
        ],
    ]);
};
