<?php

declare(strict_types=1);

namespace WebProfiler\Bridge\Slim\Middlewares;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WebProfiler\Traceables\RequestTraceable;

final class RequestDebugMiddleware implements MiddlewareInterface
{
    public function __construct(private RequestTraceable $traceable)
    {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $this->traceable->setData($request);
        return $handler->handle($request);
    }
}
