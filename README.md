# PHP Web Toolbar

[![Build Status](https://github.com/pana1990/php-web-profiler/actions/workflows//lint.yml/badge.svg)](https://github.com/pana1990/php-web-profiler/actions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

This package is under development. Please do not use in production yet 🙏

TODO

## Getting Started

```
$ composer require --dev pana1990/php-web-profiler
```
Example usage with slim framework:

```php
require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

// services
Db::setUp(); // setup schema
$pdoTraceable = new PdoTraceable('sqlite:' . __DIR__ . '/src/db/bbdd.db');
$log = (new Logger('log'))->pushHandler(new ErrorLogHandler());
$traceableLogger = new LoggerTraceable($log);

// setup for PhpWebProfiler
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
```

See full example in [here](./examples/slim).

> Note: with this setup you have two endpoints enabled (`debug` and `debug/:token`) 

## 📷 Screenshots

Index page

![Request panel](./docs/screenshots/index.png)

Request panel

![Request panel](./docs/screenshots/request.png)

Log panel

![Log panel](./docs/screenshots/log.png)

Database panel

![Database panel](./docs/screenshots/database.png)

## 📅 ROADMAP

[ ] Add support for slim

## ⚖️ LICENSE

php-web-profiler is released under the MIT License. See the bundled [LICENSE](LICENSE) for details.
