<?php

namespace A3020\StructuredDataVideo;

use A3020\StructuredDataVideo\Listener\BlockOutput;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Provider implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var Repository
     */
    private $config;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function register()
    {
        $this->listeners();
    }

    /**
     * Register event listeners.
     */
    private function listeners()
    {
        // This is used to add structured data to the video block.
        $this->dispatcher->addListener('on_block_output', function ($event) {
            /** @var BlockOutput $listener */
            $listener = $this->app->make(BlockOutput::class);
            $listener->handle($event);
        });
    }
}
