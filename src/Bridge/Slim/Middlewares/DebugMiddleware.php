<?php

declare(strict_types=1);

namespace WebProfiler\Bridge\Slim\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WebProfiler\PhpWebProfiler;

final class DebugMiddleware implements MiddlewareInterface
{
    public function __construct(private PhpWebProfiler $debugBar)
    {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $response = $handler->handle($request);
        $this->debugBar->collect();

        return $response;
    }
}
