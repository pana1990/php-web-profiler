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
    ->withBufferSizeInMb(1)
    ->withPdo($pdoTraceable)
    ->withLogger($traceableLogger)
    ->build();

$app->get('/', function (Request $request, Response $response) use ($traceableLogger, $pdoTraceable) {
    ini_set('memory_limit', '2048M');
    $response->getBody()->write(ini_get('memory_limit'));

    $pdoTraceable->exec('INSERT INTO test (title) VALUES ("test");');

    foreach (range(0, 10000) as $item) {
        $pdoTraceable->exec('SELECT * FROM test WHERE title="test" limit 1 OFFSET 0;');
        $traceableLogger->error('This is an error message');
        $traceableLogger->info('This is an error message');
        $traceableLogger->warning('This is an error message');
        $traceableLogger->error('This is an error message');
    }

    return $response;
});

$app->run();
