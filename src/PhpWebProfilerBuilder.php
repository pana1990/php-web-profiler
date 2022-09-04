<?php
declare(strict_types=1);

namespace WebProfiler;

interface PhpWebProfilerBuilder
{
    public function build(): PhpWebProfiler;
}
