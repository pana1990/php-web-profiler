<?php

use App\db\Db;
use DebugBar\Storage\FileStorage;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use WebProfiler\Bridge\Slim\Middlewares\DebugMiddleware;
use WebProfiler\Bridge\Slim\Middlewares\RequestDebugMiddleware;
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
