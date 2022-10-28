<?php

declare(strict_types=1);

namespace WebProfiler\Traceables;

use Psr\Log\LoggerInterface;
use WebProfiler\Contracts\LoggerTraceableInterface;

final class LoggerTraceable implements LoggerInterface, LoggerTraceableInterface
{
    private array $logs = [];

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function logs(): array
    {
        return $this->logs;
    }

    public function addLog(string $message, string $type, array $context = [])
    {
        $this->logs[] = [
            $type,
            $message,
            $context,
            array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 1)
        ];
    }

    public function emergency($message, array $context = []): void
    {
        $this->addLog($message, 'emergency', $context);
        $this->logger->emergency($message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->addLog($message, 'alert', $context);
        $this->logger->alert($message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->addLog($message, 'critical', $context);
        $this->logger->critical($message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->addLog($message, 'error', $context);
        $this->logger->error($message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->addLog($message, 'warning', $context);
        $this->logger->warning($message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->addLog($message, 'notice', $context);
        $this->logger->notice($message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->addLog($message, 'info', $context);
        $this->logger->info($message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->addLog($message, 'debug', $context);
        $this->logger->debug($message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $this->addLog($message, $level, $context);
        $this->logger->log($level, $message, $context);
    }
}
