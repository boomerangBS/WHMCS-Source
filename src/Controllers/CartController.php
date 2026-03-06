<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles cart / order flow pages.
 */
class CartController
{
    public function index(Request $request, Response $response): Response
    {
        $_GET['a'] = 'view';
        return $this->render($request, $response);
    }

    public function process(Request $request, Response $response): Response
    {
        return $this->render($request, $response);
    }

    public function step(Request $request, Response $response, array $args): Response
    {
        $_GET['a'] = $args['step'] ?? '';
        return $this->render($request, $response);
    }

    public function processStep(Request $request, Response $response, array $args): Response
    {
        $_GET['a'] = $args['step'] ?? '';
        return $this->render($request, $response);
    }

    public function apiCreateOrder(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        // Delegate to the legacy cart processor and return JSON result
        ob_start();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        include APP_ROOT . '/cart.php';
        ob_end_clean();

        $result = ['status' => 'success'];
        $payload = json_encode($result);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function render(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/cart.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }
}
