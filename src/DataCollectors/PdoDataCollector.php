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

        return $this->pdoTraceable->statements();
    }

    public function getName(): string
    {
        return 'pdo';
    }

    public function count(): int
    {
        return count($this->collect());
    }
}
