<?php

namespace WebProfiler\DataCollectors;

use WebProfiler\Traceables\RequestTraceable;

class RequestDataCollector extends DataCollectorAbstract implements DataCollectorToolbar
{
    private RequestTraceable $traceable;

    public function __construct($traceable)
    {
        $this->traceable = $traceable;
    }

    public function template(): string
    {
        return 'pages/request/request.html.twig';
    }

    public function collect(): array
    {
        if (!empty($this->data)) {
            return $this->data;
        }

        return $this->traceable->toData();
    }

    public function getName(): string
    {
        return 'request';
    }

    public function detail(): string
    {
        return 'detail.html.twig';
    }

    public function path(): ?string
    {
        return $this->data['path'] ?? null;
    }

    public function generalInfo()
    {
        return [
            'controller' => $this->data['callable'],
            'method' => $this->data['method'],
        ];
    }
}
