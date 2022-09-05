<?php

use App\db\Db;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use WebProfiler\Bridge\Slim\SlimPhpWebProfilerBuilder;
use WebProfiler\Traceables\LoggerTraceable;
use WebProfiler\Traceables\PdoTraceable;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

Db::setUp(); // setup schema
$pdoTraceable = new PdoTraceable('sqlite:' . __DIR__ . '/src/db/bbdd.db');
$log = (new Logger('log'))->pushHandler(new ErrorLogHandler());
$traceableLogger = new LoggerTraceable($log);

SlimPhpWebProfilerBuilder::fromApp($app)
    ->withPdo($pdoTraceable)
    ->withLogger($traceableLogger)
    ->build();

$app->get('/', function (Request $request, Response $response) use ($traceableLogger, $pdoTraceable) {
    $response->getBody()->write('Hello world!');

    $traceableLogger->error('This is an error message');

    $pdoTraceable->exec('INSERT INTO test (title) VALUES ("test");');
    $pdoTraceable->exec('SELECT * FROM test;');

    return $response;
});

$app->run();
