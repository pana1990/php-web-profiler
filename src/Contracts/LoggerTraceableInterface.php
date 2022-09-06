<?php
declare(strict_types=1);

namespace WebProfiler\Contracts;

interface LoggerTraceableInterface
{
    public function addLog(string $message, string $type, array $context = []);

    public function logs(): array;
}
