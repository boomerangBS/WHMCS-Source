<?php

declare(strict_types=1);

use App\Middleware\CacheMiddleware;
use App\Middleware\JsonBodyParserMiddleware;
use Slim\App;

return function (App $app): void {
    // Parse JSON bodies
    $app->addBodyParsingMiddleware();

    // Custom JSON body parser for extended type support
    $app->add(JsonBodyParserMiddleware::class);

    // HTTP caching headers
    $app->add(CacheMiddleware::class);

    // Add routing middleware (must be added after routes are registered)
    $app->addRoutingMiddleware();
};
