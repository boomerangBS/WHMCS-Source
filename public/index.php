<?php

declare(strict_types=1);

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/framework/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(APP_ROOT);
$dotenv->safeLoad();

// Build DI container
$containerBuilder = new ContainerBuilder();

if ($_ENV['APP_ENV'] === 'production') {
    $containerBuilder->enableCompilation(APP_ROOT . '/var/cache');
}

// Load dependencies
$dependencies = require APP_ROOT . '/app/dependencies.php';
$dependencies($containerBuilder);

$container = $containerBuilder->build();

// Create Slim App
AppFactory::setContainer($container);
$app = AppFactory::create();

// Add error middleware
$displayErrorDetails = $_ENV['APP_DEBUG'] === 'true';
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);

// Register middleware
$middleware = require APP_ROOT . '/app/middleware.php';
$middleware($app);

// Register routes
$routes = require APP_ROOT . '/app/routes.php';
$routes($app);

$app->run();
