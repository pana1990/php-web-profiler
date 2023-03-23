<?php

declare(strict_types=1);

namespace WebProfiler;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DebugBar;
use WebProfiler\DataCollectors\DataCollectorToolbar;

final class PhpWebProfiler extends DebugBar
{
    private string $prefixEndpoint = 'debug';
    private int $keepMaxLogs = 30;

    public function getToolbarCollectors(): array
    {
        return array_filter(
            $this->collectors,
            static fn (DataCollector $collector) => $collector instanceof DataCollectorToolbar
        );
    }

    public function collect(): array
    {
        if (!$this->isTraceable()) {
            return [];
        }

        $this->logRotate();

        return parent::collect();
    }

    private function isTraceable(): bool
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? null;

        if (null === $requestUri) {
            return true;
        }

        $pattern = sprintf('/^\/%s$|^\/%s\//', $this->prefixEndpoint, $this->prefixEndpoint);

        return 0 === preg_match($pattern, $requestUri);
    }

    private function logRotate(): void
    {
        $storage = $this->getStorage();
        $logs = $storage->find([], $this->keepMaxLogs + 1);

        if ($this->keepMaxLogs < count($logs)) {
            $storage->clear();
        }
    }

    public function getPrefixEndpoint(): string
    {
        return $this->prefixEndpoint;
    }
}
