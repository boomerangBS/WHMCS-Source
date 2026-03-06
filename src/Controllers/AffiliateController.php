<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles affiliate program pages and API.
 */
class AffiliateController
{
    public function index(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/affiliates.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function link(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/aff.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function apiStats(Request $request, Response $response): Response
    {
        $result  = ['affiliates' => [], 'status' => 'success'];
        $payload = json_encode($result);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
