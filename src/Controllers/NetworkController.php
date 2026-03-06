<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles network status, network issues (RSS), and server status pages.
 */
class NetworkController
{
    public function issues(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/networkissues.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function issuesRss(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/networkissuesrss.php';
        $xml = (string) ob_get_clean();
        $response->getBody()->write($xml);
        return $response->withHeader('Content-Type', 'application/rss+xml; charset=utf-8');
    }

    public function serverStatus(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/serverstatus.php';
        $html = (string) ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function announcementsRss(Request $request, Response $response): Response
    {
        ob_start();
        include APP_ROOT . '/announcementsrss.php';
        $xml = (string) ob_get_clean();
        $response->getBody()->write($xml);
        return $response->withHeader('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
