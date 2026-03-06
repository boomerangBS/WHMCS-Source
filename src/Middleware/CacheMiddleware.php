<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Adds appropriate Cache-Control headers to responses.
 *
 * Static GET routes (no session, no auth cookie) receive a short public TTL
 * so reverse proxies and browsers can cache them.  All other routes get
 * no-store to prevent leaking private data.
 */
class CacheMiddleware implements MiddlewareInterface
{
    private const PUBLIC_TTL = 60;

    /** URI prefixes that are safe to cache publicly */
    private const PUBLIC_PREFIXES = [
        '/announcements',
        '/knowledgebase',
        '/serverstatus',
        '/networkissues',
        '/feeds',
    ];

    public function process(Request $request, RequestHandler $handler): Response
    {
        $response = $handler->handle($request);

        if ($request->getMethod() !== 'GET') {
            return $response->withHeader('Cache-Control', 'no-store');
        }

        $path = $request->getUri()->getPath();

        foreach (self::PUBLIC_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $response
                    ->withHeader('Cache-Control', 'public, max-age=' . self::PUBLIC_TTL)
                    ->withHeader('Vary', 'Accept-Encoding');
            }
        }

        return $response->withHeader('Cache-Control', 'no-store');
    }
}
