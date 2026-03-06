<?php

declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Handles user authentication: login, logout, registration, password reset.
 */
class AuthController
{
    public function showLogin(Request $request, Response $response): Response
    {
        $html = $this->renderLegacyPage('login.php');
        $response->getBody()->write($html);
        return $response;
    }

    public function login(Request $request, Response $response): Response
    {
        return $this->forwardToLegacy('dologin.php', $request, $response);
    }

    public function logout(Request $request, Response $response): Response
    {
        return $this->forwardToLegacy('logout.php', $request, $response);
    }

    public function showRegister(Request $request, Response $response): Response
    {
        $html = $this->renderLegacyPage('register.php');
        $response->getBody()->write($html);
        return $response;
    }

    public function register(Request $request, Response $response): Response
    {
        return $this->forwardToLegacy('register.php', $request, $response);
    }

    public function showPasswordReset(Request $request, Response $response): Response
    {
        $html = $this->renderLegacyPage('pwreset.php');
        $response->getBody()->write($html);
        return $response;
    }

    public function passwordReset(Request $request, Response $response): Response
    {
        return $this->forwardToLegacy('pwreset.php', $request, $response);
    }

    /**
     * Render a legacy WHMCS PHP page, capturing its output.
     */
    private function renderLegacyPage(string $page): string
    {
        ob_start();
        include APP_ROOT . '/' . $page;
        return (string) ob_get_clean();
    }

    /**
     * Forward the current request to a legacy WHMCS script.
     */
    private function forwardToLegacy(string $page, Request $request, Response $response): Response
    {
        $html = $this->renderLegacyPage($page);
        $response->getBody()->write($html);
        return $response;
    }
}
