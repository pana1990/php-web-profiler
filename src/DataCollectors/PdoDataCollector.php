<?php

declare(strict_types=1);

namespace WebProfiler\DataCollectors;

use WebProfiler\Traceables\PdoTraceable;

final class PdoDataCollector extends DataCollectorAbstract
{
    private PdoTraceable $pdoTraceable;

    public function __construct(PdoTraceable $pdoTraceable)
    {
        $this->pdoTraceable = $pdoTraceable;
    }

    public function template(): string
    {
        return 'pages/pdo/pdo.html.twig';
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
        return 'database';
    }
}
