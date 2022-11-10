<?php

declare(strict_types=1);

namespace WebProfiler\DataCollectors;

use WebProfiler\Contracts\PdoTraceableInterface;

final class PdoDataCollector extends DataCollectorAbstract implements DataCollectorToolbar
{
    private PdoTraceableInterface $pdoTraceable;
    protected $xdebugLinkTemplate = 'phpstorm://open?url=file://%s&line=%s';

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
            return [
                'url' => sprintf($this->xdebugLinkTemplate, $trace['file'], $trace['line']),
                'path' => sprintf('%s:%s', $trace['file'], $trace['line']),
            ];
        };
    }
}
