<?php

namespace A3020\StructuredDataVideo\Handler;

use A3020\StructuredDataVideo\VideoPropertiesFactory;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Block\Block;

class VideoPlayerBlock implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var VideoPropertiesFactory
     */
    private $videoPropertiesFactory;

    /**
     * @param VideoPropertiesFactory $videoPropertiesFactory
     */
    public function __construct(VideoPropertiesFactory $videoPropertiesFactory)
    {
        $this->videoPropertiesFactory = $videoPropertiesFactory;
    }

    /**
     * Generates a VideoProperties object based on a Video Player block.
     *
     * @param Block $block
     *
     * @return \A3020\StructuredDataVideo\VideoProperties|null
     */
    public function getVideoProperties(Block $block)
    {
        $file = $this->getVideoFile($block);
        if (!$file) {
            return null;
        }

        $videoProperties = $this->videoPropertiesFactory->makeForFile($file);

        $thumbnail = $this->getPosterImage($block);
        if (is_object($thumbnail)) {
            $videoProperties->addThumbnail($thumbnail->getApprovedVersion()->getURL());
        }

        return $videoProperties;
    }

    /**
     * The poster image is a thumbnail the user uploaded in the Video Player block.
     *
     * @param Block $block
     *
     * @return \Concrete\Core\Entity\File\File|null
     */
    private function getPosterImage(Block $block)
    {
        /** @var \Concrete\Block\Video\Controller $controller */
        $controller = $block->getController();

        return $controller->getPosterFileObject();
    }

    /**
     * Grab one of the associated video files.
     *
     * It will take the first available video in this order:
     * mp4, webm, ogg
     *
     * @param Block $block
     *
     * @return \Concrete\Core\Entity\File\File|null
     */
    private function getVideoFile(Block $block)
    {
        /** @var \Concrete\Block\Video\Controller $controller */
        $controller = $block->getController();

        $file = $controller->getMp4FileObject();
        if (!$file) {
            $file = $controller->getWebmFileObject();
        }

        if (!$file) {
            $file = $controller->getOggFileObject();
        }

        return $file;
    }
}

