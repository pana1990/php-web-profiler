<?php

namespace WebProfiler\Controllers;

use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use WebProfiler\DataCollectors\RequestDataCollector;
use WebProfiler\PhpWebProfiler;

final class DebugController
{
    private Environment $twig;
    private PhpWebProfiler $debug;

    public function __construct(PhpWebProfiler $debug)
    {
        $this->debug = $debug;

        $loader = new FilesystemLoader(
            [__DIR__ . '/../Resources/']
        );
        $this->twig = new Environment($loader, [
            'debug' => true
        ]);
        $this->twig->addExtension(new DebugExtension());

        $this->twig->addGlobal('toolBarCollectors', array_values($this->debug->getToolbarCollectors()));
        $this->twig->addGlobal('collectors', array_values($this->debug->getCollectors()));
    }

    public function home(): string
    {
        return $this->twig->render('index.html.twig', [
            'data' => $this->debug->getStorage()->find(),
        ]);
    }

    public function page(string $token, ?string $page = null): string
    {
        $this->twig->addGlobal('token', $token);

        $data = $this->debug->getStorage()->get($token);

        foreach ($this->debug->getCollectors() as $collector) {
            $collector->load($data[$collector->getName()]);
        }

        /** @var $collector RequestDataCollector */
        if (null !== $page) {
            $collector = $this->debug->getCollector($page);
        } else {
            $collectors = $this->debug->getCollectors();
            $collector = reset($collectors);
        }

        $this->twig->addGlobal('activeCollector', $collector->getName());

        return $this->twig->render($collector->template(), ['collector' => $collector]);
    }
}
