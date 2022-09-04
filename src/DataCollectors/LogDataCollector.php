<?php

namespace WebProfiler\DataCollectors;

use Psr\Log\LoggerInterface;

final class LogDataCollector extends DataCollectorAbstract implements DataCollectorToolbar, LoggerInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function getName(): string
    {
        return 'log';
    }

    public function collect(): array
    {
        return $this->data;
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

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->data[] = ['emergency', $message, $context, debug_backtrace()];
        $this->logger->emergency($message, $context);
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->data[] = ['alert', $message, $context, debug_backtrace()];
        $this->logger->alert($message, $context);
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->data[] = ['critical', $message, $context, debug_backtrace()];
        $this->logger->critical($message, $context);
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->data[] = ['error', $message, $context, debug_backtrace()];
        $this->logger->error($message, $context);
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->data[] = ['warning', $message, $context, debug_backtrace()];
        $this->logger->warning($message, $context);
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->data[] = ['notice', $message, $context, debug_backtrace()];
        $this->logger->notice($message, $context);
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->data[] = ['info', $message, $context, debug_backtrace()];
        $this->logger->info($message, $context);
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->data[] = ['debug', $message, $context, debug_backtrace()];
        $this->logger->debug($message, $context);
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->data[] = [$level, $message, $context, debug_backtrace()];
        $this->logger->log($level, $message, $context);
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
