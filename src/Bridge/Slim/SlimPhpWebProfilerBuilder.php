<?php

declare(strict_types=1);

namespace WebProfiler\Bridge\Slim;

use DebugBar\Storage\FileStorage;
use DebugBar\Storage\StorageInterface;
use Slim\App;
use WebProfiler\Bridge\Slim\Middlewares\DebugMiddleware;
use WebProfiler\Bridge\Slim\Middlewares\RequestDebugMiddleware;
use WebProfiler\Contracts\PdoTraceableInterface;
use WebProfiler\Controllers\DebugController;
use WebProfiler\DataCollectors\LogDataCollector;
use WebProfiler\DataCollectors\PdoDataCollector;
use WebProfiler\DataCollectors\RequestDataCollector;
use WebProfiler\PhpWebProfiler;
use WebProfiler\PhpWebProfilerBuilder;
use WebProfiler\Traceables\LoggerTraceable;
use WebProfiler\Traceables\RequestTraceable;

final class SlimPhpWebProfilerBuilder implements PhpWebProfilerBuilder
{
    private ?RequestTraceable $requestTraceable = null;
    private ?LoggerTraceable $logger            = null;
    private ?PdoTraceableInterface $pdo         = null;
    private ?StorageInterface $storage          = null;

    private function __construct(
        private App $app,
        private PhpWebProfiler $phpWebProfiler,
    ) {
    }

    public static function fromApp(App $app): self
    {
        $self = new self($app, new PhpWebProfiler());
        $self->withTraceable();

        return $self;
    }

    public function withPdo(PdoTraceableInterface $pdo): self
    {
        $this->pdo = $pdo;

        return $this;
    }

    public function withTraceable(): self
    {
        $this->requestTraceable = new RequestTraceable();

        return $this;
    }

    public function withLogger(LoggerTraceable $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function withStorage(StorageInterface $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function build(): PhpWebProfiler
    {
        $this->setStorage();
        $this->addCollectors();
        $this->addMiddlewares();
        $this->addRoutes(new DebugController($this->phpWebProfiler));

        return $this->phpWebProfiler;
    }

    private function setStorage(): void
    {
        if (null === $this->storage) {
            $this->phpWebProfiler->setStorage(new FileStorage(__DIR__ . '/../../var'));
        } else {
            $this->phpWebProfiler->setStorage($this->storage);
        }
    }

    private function addRoutes(DebugController $debugController): void
    {
        $prefixEndpoint = $this->phpWebProfiler->getPrefixEndpoint();
        $this->app->get(
            "/$prefixEndpoint/{id}[/{page}]",
            function ($request, $response, array $args) use ($debugController) {
                $render = $debugController->page($args['id'], $args['page'] ?? null);
                $response->getBody()->write($render);

                return $response;
            }
        );

        $this->app->get("/$prefixEndpoint", function ($request, $response) use ($debugController) {
            $render = $debugController->home();
            $response->getBody()->write($render);

            return $response;
        });
    }

    private function addMiddlewares(): void
    {
        $this->app->addMiddleware(new DebugMiddleware($this->phpWebProfiler));
        $this->app->addMiddleware(new RequestDebugMiddleware($this->requestTraceable));
        $this->app->addRoutingMiddleware();
    }

    private function addCollectors(): void
    {
        $this->phpWebProfiler->addCollector(new RequestDataCollector($this->requestTraceable));

        if ($this->pdo) {
            $this->phpWebProfiler->addCollector(new PdoDataCollector($this->pdo));
        }

        if ($this->logger) {
            $this->phpWebProfiler->addCollector(new LogDataCollector($this->logger));
        }
    }
}
