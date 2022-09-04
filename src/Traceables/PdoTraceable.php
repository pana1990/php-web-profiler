<?php

declare(strict_types=1);

namespace WebProfiler\Traceables;

final class PdoTraceable extends \PDO
{
    private array $statements = [];

    public function exec(string $statement)
    {
        $time = time();
        $timeStart = microtime(true);

        parent::exec($statement);

        $duration = microtime(true) - $timeStart;

        $this->statements[] = [
            'time' => $time,
            'duration' => $duration,
            'sql' => $statement,
        ];
    }

    public function statements(): array
    {
        return $this->statements;
    }
}
