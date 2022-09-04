<?php

use App\db\Db;
use DebugBar\Storage\FileStorage;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use WebProfiler\Controllers\DebugController;
use WebProfiler\DataCollectors\LogDataCollector;
use WebProfiler\DataCollectors\PdoDataCollector;
use WebProfiler\DataCollectors\RequestDataCollector;
use WebProfiler\PhpWebProfiler;
use WebProfiler\Traceables\PdoTraceable;
use WebProfiler\Traceables\RequestTraceable;

require __DIR__ . '/vendor/autoload.php';

Db::setUp();
$db = new PdoTraceable("sqlite:" . __DIR__ . "/src/db/bbdd.db");

final class DebugMiddleware implements MiddlewareInterface
{
    public function __construct(
        private PhpWebProfiler $debugBar
    ) {
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $response = $handler->handle($request);
        $this->debugBar->collect();

        return $response;
    }
}

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

$app = AppFactory::create();

$log = new Logger('name');
$log->pushHandler(new ErrorLogHandler());

$debug = new PhpWebProfiler();
$debug->setStorage(new FileStorage(__DIR__ . '/var/debug'));
$traceableRequest = new RequestTraceable();
$debug->addCollector(new RequestDataCollector($traceableRequest));
$tracableLog = (new LogDataCollector())->setLogger($log);
$debug->addCollector($tracableLog);
$debug->addCollector(new PdoDataCollector($db));

$app->addMiddleware(new DebugMiddleware($debug));
$app->addMiddleware(new RequestDebugMiddleware($traceableRequest));
$app->addRoutingMiddleware();

$debugController = new DebugController($debug);

$app->get('/controller', 'App\controllers\TestController:create');

$app->get('/', function (Request $request, Response $response) use ($tracableLog, $db) {
    $response->getBody()->write("Hello world!");
    $tracableLog->error('Esto es un error');
    $db->exec('INSERT INTO test (title) VALUES ("test");');
    $db->exec('SELECT * FROM test;');

    return $response;
});

$app->get('/debug/{id}[/{page}]', function (Request $request, Response $response, array $args) use ($debugController) {
    $render = $debugController->page($args['id'], $args['page'] ?? null);
    $response->getBody()->write($render);

    return $response;
});

$app->get('/debug', function (Request $request, Response $response) use ($debugController) {
    $render = $debugController->home();
    $response->getBody()->write($render);

    return $response;
});

$app->run();
