<?php

namespace WebProfiler\Storage;

use DebugBar\Storage\StorageInterface;

interface Storage extends StorageInterface
{
    public function rotate(int $maxKeep): void;
}
