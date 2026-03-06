<?php

declare(strict_types=1);

namespace Tests;

use App\Controllers\AffiliateController;
use App\Controllers\AnnouncementController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\ClientAreaController;
use App\Controllers\ContactController;
use App\Controllers\DocumentController;
use App\Controllers\DomainController;
use App\Controllers\InvoiceController;
use App\Controllers\KnowledgebaseController;
use App\Controllers\NetworkController;
use App\Controllers\SupportController;
use App\Middleware\CacheMiddleware;
use App\Middleware\JsonBodyParserMiddleware;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\RequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

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
        $app->add(CacheMiddleware::class);
        $app->addRoutingMiddleware();

        $routes = require APP_ROOT . '/app/routes.php';
        $routes($app);

        return $app;
    }

    // ── Core app ────────────────────────────────────────────────────────────────

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

    // ── Route registration ──────────────────────────────────────────────────────

    public function testLoginRouteIsRegistered(): void
    {
        $app      = $this->createSlimApp();
        $patterns = array_map(fn($r) => $r->getPattern(), $app->getRouteCollector()->getRoutes());
        $this->assertContains('/login', $patterns);
    }

    public function testApiRoutesAreRegistered(): void
    {
        $app      = $this->createSlimApp();
        $patterns = array_map(fn($r) => $r->getPattern(), $app->getRouteCollector()->getRoutes());

        $this->assertContains('/api/v1/orders', $patterns);
        $this->assertContains('/api/v1/tickets', $patterns);
        $this->assertContains('/api/v1/domains/check', $patterns);
        $this->assertContains('/api/v1/invoices', $patterns);
        $this->assertContains('/api/v1/affiliates/stats', $patterns);
    }

    public function testNewRoutesAreRegistered(): void
    {
        $app      = $this->createSlimApp();
        $patterns = array_map(fn($r) => $r->getPattern(), $app->getRouteCollector()->getRoutes());

        $this->assertContains('/networkissues', $patterns);
        $this->assertContains('/serverstatus', $patterns);
        $this->assertContains('/viewinvoice/{id}/{key}', $patterns);
        $this->assertContains('/affiliates', $patterns);
        $this->assertContains('/unsubscribe', $patterns);
    }

    // ── Controllers ─────────────────────────────────────────────────────────────

    public function testDomainControllerApiReturnsJson(): void
    {
        $controller = new DomainController();
        $request    = (new RequestFactory())->createRequest('GET', '/api/v1/domains/check');
        $response   = (new ResponseFactory())->createResponse();

        $request = $request->withQueryParams(['domain' => 'example.com']);
        $result  = $controller->apiCheck($request, $response);

        $this->assertSame('application/json', $result->getHeaderLine('Content-Type'));
        $data = json_decode((string) $result->getBody(), true);
        $this->assertSame('example.com', $data['domain']);
        $this->assertSame('success', $data['status']);
    }

    public function testInvoiceControllerApiListReturnsJson(): void
    {
        $controller = new InvoiceController();
        $request    = (new RequestFactory())->createRequest('GET', '/api/v1/invoices');
        $response   = (new ResponseFactory())->createResponse();

        $result = $controller->apiList($request, $response);

        $this->assertSame('application/json', $result->getHeaderLine('Content-Type'));
        $data = json_decode((string) $result->getBody(), true);
        $this->assertSame('success', $data['status']);
        $this->assertIsArray($data['invoices']);
    }

    public function testAffiliateControllerApiStatsReturnsJson(): void
    {
        $controller = new AffiliateController();
        $request    = (new RequestFactory())->createRequest('GET', '/api/v1/affiliates/stats');
        $response   = (new ResponseFactory())->createResponse();

        $result = $controller->apiStats($request, $response);

        $this->assertSame('application/json', $result->getHeaderLine('Content-Type'));
        $data = json_decode((string) $result->getBody(), true);
        $this->assertSame('success', $data['status']);
    }

    public function testControllersAreInstantiable(): void
    {
        $this->assertInstanceOf(AuthController::class,          new AuthController());
        $this->assertInstanceOf(ClientAreaController::class,    new ClientAreaController());
        $this->assertInstanceOf(CartController::class,          new CartController());
        $this->assertInstanceOf(DomainController::class,        new DomainController());
        $this->assertInstanceOf(SupportController::class,       new SupportController());
        $this->assertInstanceOf(KnowledgebaseController::class, new KnowledgebaseController());
        $this->assertInstanceOf(AnnouncementController::class,  new AnnouncementController());
        $this->assertInstanceOf(ContactController::class,       new ContactController());
        $this->assertInstanceOf(InvoiceController::class,       new InvoiceController());
        $this->assertInstanceOf(AffiliateController::class,     new AffiliateController());
        $this->assertInstanceOf(NetworkController::class,       new NetworkController());
        $this->assertInstanceOf(DocumentController::class,      new DocumentController());
    }

    // ── Middleware ───────────────────────────────────────────────────────────────

    public function testJsonBodyParserMiddlewareIsInstantiable(): void
    {
        $this->assertInstanceOf(JsonBodyParserMiddleware::class, new JsonBodyParserMiddleware());
    }

    public function testCacheMiddlewareIsInstantiable(): void
    {
        $this->assertInstanceOf(CacheMiddleware::class, new CacheMiddleware());
    }

    // ── License stub ────────────────────────────────────────────────────────────

    public function testLicenseStubAlwaysValid(): void
    {
        // The License class must be autoloaded via the WHMCS vendor autoloader
        $whmcsAutoload = APP_ROOT . '/vendor/autoload.php';
        if (!file_exists($whmcsAutoload)) {
            $this->markTestSkipped('WHMCS vendor autoload not available.');
        }
        require_once $whmcsAutoload;

        if (!class_exists(\WHMCS\License::class)) {
            $this->markTestSkipped('WHMCS\License class not available.');
        }

        $license = new \WHMCS\License();
        $this->assertTrue($license->validate());
        $this->assertFalse($license->isUnlicensed());
        $this->assertSame('', $license->getBanner());
        $this->assertNull($license->getClientLimitNotificationAttributes());
        $this->assertFalse($license->isClientLimitsEnabled());
        $this->assertSame(-1, $license->getClientLimit());
        $this->assertFalse($license->isNearClientLimit());
        // checkFile must not throw for any input (old hash or random string)
        $this->assertInstanceOf(\WHMCS\License::class, $license->checkFile('a896faf2c31f2acd47b0eda0b3fd6070958f1161'));
        $this->assertInstanceOf(\WHMCS\License::class, $license->checkFile('invalid-hash-value'));
    }
}
