<?php

namespace A3020\StructuredDataVideo\Page\Sitemap\Element;

use SimpleXMLElement;

class SitemapPageVideo extends \Concrete\Core\Page\Sitemap\Element\SitemapPage
{
    /** @var Video[] */
    protected $videoElements = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Page\Sitemap\Element\SitemapElement::toXmlLines()
     */
    public function toXmlLines($indenter = '  ')
    {
        $result = parent::toXmlLines($indenter);

        if ($result) {
            // Remove closing tag (temporarily).
            $url = array_pop($result);

            foreach ($this->getVideoElements() as $videoElement) {
                $xml = $videoElement->toXmlLines($indenter . $indenter);
                if ($xml) {
                    $result = array_merge($result, $xml);
                }
            }

            // Append closing tag again.
            $result[] = $url;
        }

        return $result;
    }

    /**
     * @param Video $video
     */
    public function addVideoElement(Video $video)
    {
        $this->videoElements[] = $video;
    }

    /**
     * @return Video[]
     */
    public function getVideoElements()
    {
        return $this->videoElements;
    }
}
