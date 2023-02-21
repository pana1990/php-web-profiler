<?php

declare(strict_types=1);

namespace WebProfiler\DataCollectors;

use WebProfiler\Contracts\PdoTraceableInterface;

final class PdoDataCollector extends DataCollectorAbstract implements DataCollectorToolbar
{
    private PdoTraceableInterface $pdoTraceable;

    public function __construct(PdoTraceableInterface $pdoTraceable)
    {
        $this->pdoTraceable = $pdoTraceable;
    }

    public function template(): string
    {
        return 'pages/pdo/pdo.html.twig';
    }

    public function detail(): string
    {
        return 'detail.html.twig';
    }

    public function collect(): array
    {
        if (!empty($this->data)) {
            return $this->data;
        }

        $statements = $this->pdoTraceable->statements();
        foreach ($statements as &$statement) {
            $statement['trace'] = array_map($this->trace(), $statement['trace']);
        }
        return $statements;
    }

    public function getName(): string
    {
        return 'pdo';
    }

    public function count(): int
    {
        return count($this->collect());
    }

    public function trace(): \Closure
    {
        return function (array $trace): array {
            $file = $trace['file'] ?? null;
            $line = $trace['line'] ?? null;
            $class = $trace['class'] ?? null;

            if (true === isset($file, $line)) {
                return [
                    'url'  => sprintf($this->xdebugLinkTemplate, $file, $line),
                    'path' => sprintf('%s:%s', $file, $line),
                ];
            }

            if (true === isset($class)) {
                return [
                    'url' => null,
                    'path' => sprintf('%s:%s', $class, $line),
                ];
            }

            return [
                'url' => null,
                'path' => 'Unkown',
            ];
        };
    }
}
