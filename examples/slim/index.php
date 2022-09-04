<?php

use App\db\Db;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use WebProfiler\Bridge\Slim\SlimPhpWebProfilerBuilder;
use WebProfiler\DataCollectors\LogDataCollector;
use WebProfiler\Traceables\PdoTraceable;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

Db::setUp();
$db = new PdoTraceable('sqlite:' . __DIR__ . '/src/db/bbdd.db');
$log = (new Logger('log'))->pushHandler(new ErrorLogHandler());

SlimPhpWebProfilerBuilder::fromApp($app)
    ->withPdo($db)
    ->build();

$app->get('/', function (Request $request, Response $response) use ($db) {
    $response->getBody()->write('Hello world!');

    $db->exec('INSERT INTO test (title) VALUES ("test");');
    $db->exec('SELECT * FROM test;');

    return $response;
});

$app->run();
