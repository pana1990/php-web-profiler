<?php

namespace WebProfiler\DataCollectors;

use WebProfiler\Traceables\LoggerTraceable;

final class LogDataCollector extends DataCollectorAbstract implements DataCollectorToolbar
{
    public function __construct(private LoggerTraceable $loggerTraceable)
    {
    }

    public function getName(): string
    {
        return 'log';
    }

    public function collect(): array
    {
        if (!empty($this->data)) {
            return $this->data;
        }

        return $this->loggerTraceable->logs();
    }

    public function detail(): string
    {
        return 'detail.html.twig';
    }

    public function template(): string
    {
        return 'pages/log/log.html.twig';
    }

    public function logs(): array
    {
        return $this->data;
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function style(array $value): string
    {
        $map = [
            'warning' => 'table-warning',
            'error' => 'table-danger',
            'critical' => 'table-danger',
            'alert' => 'table-danger',
        ];

        return $map[$value[0]] ?? '';
    }

    public function trace(array $value): string
    {
        $trace = $value[3];

        $lastTrace = reset($trace);

        return $lastTrace['file'];
    }

    public function ideLink(array $value): string
    {
        $trace = $value[3];

        $lastTrace = reset($trace);

        return sprintf(
            'phpstorm://open?url=file://%s&line=%s',
            $lastTrace['file'],
            $lastTrace['line']
        );
    }

    public function levels(): array
    {
        return [
            'debug',
            'info',
            'notice',
            'warning',
            'error',
            'critical',
            'alert',
            'emergency',
        ];
    }
}
