<?php

declare(strict_types=1);

namespace WebProfiler\Traits;

trait BufferSize
{
    private ?int $bufferSize = null;

    public function isBufferSizeExceeded(): bool
    {
        return $this->bufferSize < memory_get_usage();
    }

    public function setBufferSize(int $bufferSize): void
    {
        $this->bufferSize = $bufferSize;
    }
}
