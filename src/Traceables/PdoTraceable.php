<?php

declare(strict_types=1);

namespace WebProfiler\Traceables;

use WebProfiler\Contracts\PdoTraceableInterface;
use WebProfiler\Traits\BufferSize;

final class PdoTraceable extends \PDO implements PdoTraceableInterface
{
    use BufferSize;

    private array $statements = [];

    public function exec(string $statement)
    {
        $time = time();
        $timeStart = microtime(true);

        parent::exec($statement);

        $duration = microtime(true) - $timeStart;

        if ($this->isBufferSizeExceeded()) {
            return;
        }

        $this->addStatement([
            'time' => $time,
            'duration' => $duration,
            'sql' => $statement,
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
