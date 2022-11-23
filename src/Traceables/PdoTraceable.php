<?php

declare(strict_types=1);

namespace WebProfiler\Traceables;

use WebProfiler\Contracts\PdoTraceableInterface;

final class PdoTraceable extends \PDO implements PdoTraceableInterface
{
    private array $statements = [];

    public function exec(string $statement)
    {
        $time = time();
        $timeStart = microtime(true);

        parent::exec($statement);

        $duration = microtime(true) - $timeStart;

        $this->addStatement([
            'time' => $time,
            'duration' => $duration,
            'sql' => $statement,
            'trace' => debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 5),
        ]);
    }

    public function statements(): array
    {
        return $this->statements;
    }

    public function addStatement(array $data): void
    {
        $this->statements[] = $data;
    }
}
