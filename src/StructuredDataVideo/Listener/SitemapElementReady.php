<?php

namespace A3020\StructuredDataVideo\Listener;

use A3020\StructuredDataVideo\Handler\VideoPlayerBlock;
use A3020\StructuredDataVideo\Page\Sitemap\Element\SitemapHeader;
use A3020\StructuredDataVideo\Page\Sitemap\Element\SitemapPageVideo;
use A3020\StructuredDataVideo\Page\Sitemap\Element\Video as VideoElement;
use A3020\StructuredDataVideo\VideoBlocks;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Block\Block;
use Concrete\Core\Page\Page;
use Exception;
use Psr\Log\LoggerInterface;

class SitemapElementReady implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var VideoBlocks
     */
    private $videoBlocks;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var VideoPlayerBlock
     */
    private $videoPlayerBlock;

    public function __construct(VideoBlocks $videoBlocks, LoggerInterface $logger, VideoPlayerBlock $videoPlayerBlock)
    {
        $this->videoBlocks = $videoBlocks;
        $this->logger = $logger;
        $this->videoPlayerBlock = $videoPlayerBlock;
    }

    /**
     * @param \Concrete\Core\Page\Sitemap\Event\ElementReadyEvent $event
     *
     * @return \Symfony\Component\EventDispatcher\GenericEvent
     */
    public function handle($event)
    {
        try {
            // This website doesn't have any Video blocks.
            if (!$this->videoBlocks->getBlockIds()) {
                return $event;
            }

            // Transform the SitemapHeader to add a video namespace.
            $element = $event->getElement();
            if ($element instanceof \Concrete\Core\Page\Sitemap\Element\SitemapHeader) {
                return $this->transformSitemapHeader($event);
            }

            // Skip other kind of elements (e.g. footer).
            if (!$element instanceof \Concrete\Core\Page\Sitemap\Element\SitemapPage) {
                return $event;
            }

            // Check if this particular page has any videos attached to it.
            $videosForPage = $this->getVideosFor($element->getPage());
            if (!count($videosForPage)) {
                return $event;
            }

            // Create a new element. We can't simply add a node to the SitemapPage object.
            $sitemapPageVideo = new SitemapPageVideo(
                $element->getPage(),
                $element->getUrl(),
                $element->getLastModifiedAt(),
                $element->getChangeFrequency(),
                $element->getPriority()
            );

            foreach ($videosForPage as $videoProperties) {
                $sitemapPageVideo->addVideoElement(
                    new VideoElement($videoProperties)
                );
            }

            $event->setElement($sitemapPageVideo);

            return $event;
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return $event;
    }

    /**
     * @param \Concrete\Core\Page\Page $page
     *
     * @return \A3020\StructuredDataVideo\VideoProperties[]
     */
    private function getVideosFor(Page $page)
    {
        $videoBlockIds = $this->videoBlocks->videoBlocksForPage($page);

        $videos = [];
        foreach ($videoBlockIds as $blockId) {
            $block = Block::getByID($blockId);
            if (!$block) {
                continue;
            }

            $properties = $this->videoPlayerBlock->getVideoProperties($block);
            if ($properties) {
                $videos[] = $properties;
            }
        }

        return $videos;
    }

    /**
     * Add a video namespace to the XML header.
     *
     * @param \Concrete\Core\Page\Sitemap\Event\ElementReadyEvent $event
     *
     * @return \Concrete\Core\Page\Sitemap\Event\ElementReadyEvent
     */
    private function transformSitemapHeader($event)
    {
        $newHeader = $this->app->make(SitemapHeader::class, [
            $event->getElement()->isIsMultilingual(),
        ]);

        $event->setElement($newHeader);

        return $event;
    }
}
