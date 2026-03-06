<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Serves the client area portal pages.
 */
class ClientAreaController
{
    public function home(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/index.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function index(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/clientarea.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function handle(Request $request, Response $response, array $args): Response
    {
        $_GET['action'] = $args['action'] ?? '';
        ob_start();
        include APP_ROOT . '/clientarea.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }
}
