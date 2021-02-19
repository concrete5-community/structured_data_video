<?php

namespace A3020\StructuredDataVideo\Listener;

use A3020\StructuredDataVideo\Handler\VideoPlayerBlock;
use A3020\StructuredDataVideo\StructuredData;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Exception;
use Psr\Log\LoggerInterface;

class BlockOutput implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param \Concrete\Core\Block\Events\BlockOutput $event
     *
     * @return \Concrete\Core\Block\Events\BlockOutput
     */
    public function handle(\Concrete\Core\Block\Events\BlockOutput $event)
    {
        try {
            $block = $event->getBlock();

            // This can occur when adding a new block.
            if (!is_object($block)) {
                return $event;
            }

            $videoProperties = null;
            if ($block->getBlockTypeHandle() === 'video') {
                /** @var VideoPlayerBlock $handler */
                $handler = $this->app->make(VideoPlayerBlock::class);
                $videoProperties = $handler->getVideoProperties($block);
            }

            if ($videoProperties) {
                $structuredData = new StructuredData();
                $structuredData->setVideoProperties($videoProperties);

                $event->setContents(
                    $this->addStructuredData(
                        $event->getContents(),
                        $structuredData
                    )
                );
            }
        } catch (Exception $e) {
            $this->logger->debug($e->getMessage());
        }

        return $event;
    }

    /**
     * Appends the structured data to the block's HTML output.
     *
     * @param string $content
     * @param StructuredData $structuredData
     *
     * @return string
     */
    private function addStructuredData($content, StructuredData $structuredData)
    {
        if (!$structuredData->isComplete()) {
            return $content;
        }

        return $content . '<script type="application/ld+json"> '. $structuredData->toJson() . '</script>';
    }
}
