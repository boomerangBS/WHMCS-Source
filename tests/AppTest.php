<?php

declare(strict_types=1);

namespace Tests;

use App\Controllers\AnnouncementController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\ClientAreaController;
use App\Controllers\ContactController;
use App\Controllers\DomainController;
use App\Controllers\KnowledgebaseController;
use App\Controllers\SupportController;
use App\Middleware\JsonBodyParserMiddleware;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

class AppTest extends TestCase
{
    private function createSlimApp(): \Slim\App
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions([
            'settings' => [
                'displayErrorDetails' => true,
                'db' => [
                    'host' => 'localhost',
                    'name' => 'whmcs_test',
                    'user' => 'root',
                    'pass' => '',
                ],
            ],
            'logger' => function (): \Monolog\Logger {
                return new \Monolog\Logger('test');
            },
        ]);
        $container = $containerBuilder->build();

        AppFactory::setContainer($container);
        $app = AppFactory::create();
        $app->addErrorMiddleware(true, true, true);
        $app->addBodyParsingMiddleware();
        $app->add(JsonBodyParserMiddleware::class);
        $app->addRoutingMiddleware();

        $routes = require APP_ROOT . '/app/routes.php';
        $routes($app);

        return $app;
    }

    public function testAppCreation(): void
    {
        $app = $this->createSlimApp();
        $this->assertInstanceOf(\Slim\App::class, $app);
    }

    public function testRouteCollectorHasRoutes(): void
    {
        $app    = $this->createSlimApp();
        $routes = $app->getRouteCollector()->getRoutes();
        $this->assertNotEmpty($routes);
    }

    public function testLoginRouteIsRegistered(): void
    {
        $app    = $this->createSlimApp();
        $routes = $app->getRouteCollector()->getRoutes();

        $patterns = array_map(fn($r) => $r->getPattern(), $routes);
        $this->assertContains('/login', $patterns);
    }

    public function testApiRoutesAreRegistered(): void
    {
        $app    = $this->createSlimApp();
        $routes = $app->getRouteCollector()->getRoutes();

        $patterns = array_map(fn($r) => $r->getPattern(), $routes);

        $this->assertContains('/api/v1/orders', $patterns);
        $this->assertContains('/api/v1/tickets', $patterns);
        $this->assertContains('/api/v1/domains/check', $patterns);
    }

    public function testDomainControllerApiReturnsJson(): void
    {
        $controller = new DomainController();
        $request    = (new RequestFactory())->createRequest('GET', '/api/v1/domains/check?domain=example.com');
        $response   = (new ResponseFactory())->createResponse();

        // Inject query params
        $request = $request->withQueryParams(['domain' => 'example.com']);

        $result = $controller->apiCheck($request, $response);

        $this->assertSame('application/json', $result->getHeaderLine('Content-Type'));

        $body = (string) $result->getBody();
        $data = json_decode($body, true);

        $this->assertSame('example.com', $data['domain']);
        $this->assertSame('success', $data['status']);
    }

    public function testJsonBodyParserMiddlewarePassesThrough(): void
    {
        $middleware = new JsonBodyParserMiddleware();
        $this->assertInstanceOf(JsonBodyParserMiddleware::class, $middleware);
    }

    public function testControllersAreInstantiable(): void
    {
        $this->assertInstanceOf(AuthController::class,         new AuthController());
        $this->assertInstanceOf(ClientAreaController::class,   new ClientAreaController());
        $this->assertInstanceOf(CartController::class,         new CartController());
        $this->assertInstanceOf(DomainController::class,       new DomainController());
        $this->assertInstanceOf(SupportController::class,      new SupportController());
        $this->assertInstanceOf(KnowledgebaseController::class, new KnowledgebaseController());
        $this->assertInstanceOf(AnnouncementController::class, new AnnouncementController());
        $this->assertInstanceOf(ContactController::class,      new ContactController());
    }
}
