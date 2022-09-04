<?php

declare(strict_types=1);

namespace App\controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class TestController
{
    public function create(Request $request): Response
    {
        return new \Slim\Psr7\Response(200);
    }
}
