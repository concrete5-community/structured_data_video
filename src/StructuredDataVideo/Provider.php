<?php

namespace A3020\StructuredDataVideo;

use A3020\StructuredDataVideo\Listener\BlockOutput;
use A3020\StructuredDataVideo\Listener\SitemapElementReady;
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

    public function __construct(EventDispatcherInterface $dispatcher, Repository $config)
    {
        $this->dispatcher = $dispatcher;
        $this->config = $config;
    }

    public function register()
    {
        $this->listeners();

        $this->app->singleton(VideoBlocks::class,VideoBlocks::class);
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

        // The on_sitemap_xml_event doesn't work (correctly) in earlier versions.
        if (version_compare($this->config->get('concrete.version_installed'), '8.5.0a2', '>=')) {
            $this->dispatcher->addListener('on_sitemap_xml_element', function ($event) {
                /** @var SitemapElementReady $listener */
                $listener = $this->app->make(SitemapElementReady::class);
                $listener->handle($event);
            });
        }
    }
}
