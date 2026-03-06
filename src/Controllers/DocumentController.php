<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles quote, email and download views.
 */
class DocumentController
{
    public function viewQuote(Request $request, Response $response, array $args): Response
    {
        $_GET['qid']  = $args['id']  ?? '';
        $_GET['key']  = $args['key'] ?? '';
        ob_start();
        include APP_ROOT . '/viewquote.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function viewEmail(Request $request, Response $response, array $args): Response
    {
        $_GET['uid']  = $args['uid']  ?? '';
        $_GET['eid']  = $args['eid']  ?? '';
        $_GET['key']  = $args['key']  ?? '';
        ob_start();
        include APP_ROOT . '/viewemail.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function download(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/dl.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function unsubscribe(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/unsubscribe.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }
}
