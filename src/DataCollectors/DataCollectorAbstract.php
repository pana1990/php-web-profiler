<?php

namespace WebProfiler\DataCollectors;

use DebugBar\DataCollector\DataCollector;

abstract class DataCollectorAbstract extends DataCollector
{
    protected array $data = [];

    abstract public function template(): string;

    public function load(?array $data): void
    {
        $this->data = $data ?? [];
    }
}
