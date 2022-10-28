<?php

namespace WebProfiler\Contracts;

interface PdoTraceableInterface
{
    public function addStatement(array $data): void;
    public function statements(): array;
}
