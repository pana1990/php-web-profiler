<?php

declare(strict_types=1);

namespace WebProfiler\Traceables;

use Psr\Log\LoggerInterface;

final class LoggerTraceable implements LoggerInterface
{
    private array $logs = [];

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function logs(): array
    {
        return $this->logs;
    }

    public function emergency(\Stringable|string $message, array $context = []): void
    {
        $this->logs[] = ['emergency', $message, $context, debug_backtrace()];
        $this->logger->emergency($message, $context);
    }

    public function alert(\Stringable|string $message, array $context = []): void
    {
        $this->logs[] = ['alert', $message, $context, debug_backtrace()];
        $this->logger->alert($message, $context);
    }

    public function critical(\Stringable|string $message, array $context = []): void
    {
        $this->logs[] = ['critical', $message, $context, debug_backtrace()];
        $this->logger->critical($message, $context);
    }

    public function error(\Stringable|string $message, array $context = []): void
    {
        $this->logs[] = ['error', $message, $context, debug_backtrace()];
        $this->logger->error($message, $context);
    }

    public function warning(\Stringable|string $message, array $context = []): void
    {
        $this->logs[] = ['warning', $message, $context, debug_backtrace()];
        $this->logger->warning($message, $context);
    }

    public function notice(\Stringable|string $message, array $context = []): void
    {
        $this->logs[] = ['notice', $message, $context, debug_backtrace()];
        $this->logger->notice($message, $context);
    }

    public function info(\Stringable|string $message, array $context = []): void
    {
        $this->logs[] = ['info', $message, $context, debug_backtrace()];
        $this->logger->info($message, $context);
    }

    public function debug(\Stringable|string $message, array $context = []): void
    {
        $this->logs[] = ['debug', $message, $context, debug_backtrace()];
        $this->logger->debug($message, $context);
    }

    public function log($level, \Stringable|string $message, array $context = []): void
    {
        $this->logs[] = [$level, $message, $context, debug_backtrace()];
        $this->logger->log($level, $message, $context);
    }
}
