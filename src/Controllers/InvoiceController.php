<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles invoice view, download, and payment.
 */
class InvoiceController
{
    public function view(Request $request, Response $response, array $args): Response
    {
        $_GET['id']  = $args['id']  ?? '';
        $_GET['key'] = $args['key'] ?? '';
        ob_start();
        include APP_ROOT . '/viewinvoice.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function apiGet(Request $request, Response $response, array $args): Response
    {
        $result  = ['invoice' => ['id' => (int) ($args['id'] ?? 0)], 'status' => 'success'];
        $payload = json_encode($result);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function apiList(Request $request, Response $response): Response
    {
        $result  = ['invoices' => [], 'status' => 'success'];
        $payload = json_encode($result);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
