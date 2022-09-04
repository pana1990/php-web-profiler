<?php

declare(strict_types=1);

namespace WebProfiler\Traceables;

use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\Route;

final class RequestTraceable
{
    private ?string $callable = null;
    private ?string $method = null;
    private ?string $path = null;
    private array $queryParams = [];
    private array $postParams = [];
    private array $serverParams = [];

    public function toData(): array
    {
        return [
            'callable' => $this->callable,
            'method' => $this->method,
            'path' => $this->path,
            'get' => $this->queryParams,
            'post' => $this->postParams,
            'server' => $this->serverParams,
        ];
    }

    public function setData(ServerRequestInterface $request): void
    {
        /** @var Route $route */
        $route = $request->getAttribute('__route__');
        $this->callable = is_string($route->getCallable()) ? $route->getCallable() : 'callable';
        $this->path = $route->getPattern();
        $this->method = $route->getMethods()[0] ?? null;
        $this->queryParams = $_GET;
        $this->postParams = $_POST;
        $this->serverParams = $_SERVER;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function getCallable(): ?string
    {
        return $this->callable;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }
}
