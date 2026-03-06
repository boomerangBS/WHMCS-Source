<?php

declare(strict_types=1);

use App\Middleware\JsonBodyParserMiddleware;
use Slim\App;

return function (App $app): void {
    // Parse JSON bodies
    $app->addBodyParsingMiddleware();

    // Custom JSON body parser for extended type support
    $app->add(JsonBodyParserMiddleware::class);

    // Add routing middleware (must be added after routes are registered)
    $app->addRoutingMiddleware();
};
