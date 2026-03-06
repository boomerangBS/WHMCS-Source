<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles support ticket listing, creation, and viewing.
 */
class SupportController
{
    public function index(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/supporttickets.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function showCreate(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/submitticket.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function create(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/submitticket.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function view(Request $request, Response $response, array $args): Response
    {
        $_GET['id']  = $args['id']  ?? '';
        $_GET['key'] = $args['key'] ?? '';
        ob_start();
        include APP_ROOT . '/viewticket.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function reply(Request $request, Response $response, array $args): Response
    {
        $_GET['id']  = $args['id']  ?? '';
        $_GET['key'] = $args['key'] ?? '';
        ob_start();
        include APP_ROOT . '/viewticket.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function apiList(Request $request, Response $response): Response
    {
        $result  = ['tickets' => [], 'status' => 'success'];
        $payload = json_encode($result);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function apiCreate(Request $request, Response $response): Response
    {
        $result  = ['ticket' => null, 'status' => 'success'];
        $payload = json_encode($result);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
