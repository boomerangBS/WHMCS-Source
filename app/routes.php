<?php

declare(strict_types=1);

use App\Controllers\AnnouncementController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\ClientAreaController;
use App\Controllers\ContactController;
use App\Controllers\DomainController;
use App\Controllers\KnowledgebaseController;
use App\Controllers\SupportController;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return function (App $app): void {
    // ─── Authentication ────────────────────────────────────────────────────────
    $app->get('/login',    [AuthController::class, 'showLogin']);
    $app->post('/login',   [AuthController::class, 'login']);
    $app->get('/logout',   [AuthController::class, 'logout']);
    $app->get('/register', [AuthController::class, 'showRegister']);
    $app->post('/register', [AuthController::class, 'register']);
    $app->get('/pwreset',  [AuthController::class, 'showPasswordReset']);
    $app->post('/pwreset', [AuthController::class, 'passwordReset']);

    // ─── Client Area ────────────────────────────────────────────────────────────
    $app->get('/',          [ClientAreaController::class, 'home']);
    $app->get('/clientarea', [ClientAreaController::class, 'index']);
    $app->map(['GET', 'POST'], '/clientarea/{action}', [ClientAreaController::class, 'handle']);

    // ─── Cart / Ordering ────────────────────────────────────────────────────────
    $app->get('/cart',              [CartController::class, 'index']);
    $app->post('/cart',             [CartController::class, 'process']);
    $app->get('/cart/{step}',       [CartController::class, 'step']);
    $app->post('/cart/{step}',      [CartController::class, 'processStep']);

    // ─── Domains ────────────────────────────────────────────────────────────────
    $app->get('/domainchecker',  [DomainController::class, 'index']);
    $app->post('/domainchecker', [DomainController::class, 'check']);

    // ─── Support ────────────────────────────────────────────────────────────────
    $app->get('/supporttickets',           [SupportController::class, 'index']);
    $app->get('/submitticket',             [SupportController::class, 'showCreate']);
    $app->post('/submitticket',            [SupportController::class, 'create']);
    $app->get('/viewticket/{id}/{key}',    [SupportController::class, 'view']);
    $app->post('/viewticket/{id}/{key}',   [SupportController::class, 'reply']);

    // ─── Knowledge Base ─────────────────────────────────────────────────────────
    $app->get('/knowledgebase',           [KnowledgebaseController::class, 'index']);
    $app->get('/knowledgebase/{id}/{slug}', [KnowledgebaseController::class, 'article']);

    // ─── Announcements ──────────────────────────────────────────────────────────
    $app->get('/announcements',       [AnnouncementController::class, 'index']);
    $app->get('/announcements/{id}',  [AnnouncementController::class, 'show']);

    // ─── Contact ────────────────────────────────────────────────────────────────
    $app->get('/contact',  [ContactController::class, 'show']);
    $app->post('/contact', [ContactController::class, 'submit']);

    // ─── API group ──────────────────────────────────────────────────────────────
    $app->group('/api/v1', function (RouteCollectorProxy $group): void {
        $group->post('/orders',   [CartController::class, 'apiCreateOrder']);
        $group->get('/tickets',   [SupportController::class, 'apiList']);
        $group->post('/tickets',  [SupportController::class, 'apiCreate']);
        $group->get('/domains/check', [DomainController::class, 'apiCheck']);
    });
};
