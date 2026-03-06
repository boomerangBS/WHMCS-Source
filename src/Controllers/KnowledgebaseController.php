<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles the knowledge base article listing and single-article view.
 */
class KnowledgebaseController
{
    public function index(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/knowledgebase.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function article(Request $request, Response $response, array $args): Response
    {
        $_GET['id'] = $args['id'] ?? '';
        ob_start();
        include APP_ROOT . '/knowledgebase.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }
}
