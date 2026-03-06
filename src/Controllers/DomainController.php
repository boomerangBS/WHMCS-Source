<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles domain availability checks.
 */
class DomainController
{
    public function index(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/domainchecker.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function check(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/domainchecker.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function apiCheck(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $domain = $params['domain'] ?? '';

        $result = [
            'domain'    => $domain,
            'available' => true,
            'status'    => 'success',
        ];

        $payload = json_encode($result);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
