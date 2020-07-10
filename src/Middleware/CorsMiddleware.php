<?php
namespace App\Middleware;

use Cake\Http\Cookie\Cookie;
use Cake\I18n\Time;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        // Calling $handler->handle() delegates control to the *next* middleware
        // In your application's queue.
        $response = $handler->handle($request);

        $response = $response->cors($request)
        ->allowOrigin(['*'])
        ->allowMethods(['GET', 'POST', 'OPTIONS'])
        ->allowCredentials()
        ->allowHeaders(['Content-Type', 'x-auth-token'])
        ->exposeHeaders(['Link'])
        ->maxAge(300)
        ->build();
        return $response;
    }
}
?>