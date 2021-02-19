<?php

namespace A3020\StructuredDataVideo\Page\Sitemap\Element;

use Concrete\Core\Page\Sitemap\Element\SitemapElement;
use SimpleXMLElement;

class Video extends SitemapElement
{
    /** @var \A3020\StructuredDataVideo\VideoProperties */
    protected $video;

    /**
     * @param \A3020\StructuredDataVideo\VideoProperties $video
     */
    public function __construct($video)
    {
        $this->video = $video;
    }

    /**
     * Returns the XML representation of this element.
     *
     * @param string $indenter The string used to indent the XML
     *
     * @return string[]|null Returns NULL in case no data should be generated, the XML lines otherwise
     */
    public function toXmlLines($indenter = '  ')
    {
        $indenterSub = $indenter . $indenter;

        if (!$this->video->isComplete()) {
            return null;
        }

        $result = [];
        $result[] = "{$indenter}<video:video>";

        // Required attributes
        $result[] = "{$indenterSub}<video:thumbnail_loc>" . $this->video->getFirstThumbnail() . "</video:thumbnail_loc>";
        $result[] = "{$indenterSub}<video:title>" . $this->video->getName() . "</video:title>";
        $result[] = "{$indenterSub}<video:description>" . $this->video->getShortenedDescription() . "</video:description>";
        $result[] = "{$indenterSub}<video:content_loc>" . $this->video->getContentUrl() . "</video:content_loc>";
        $result[] = "{$indenterSub}<video:publication_date>" . $this->video->getUploadDateTransformed() . "</video:publication_date>";

        // Recommended attributes
        if ($this->video->getDuration()) {
            $result[] = "{$indenterSub}<video:duration>" . $this->video->getDuration() . "</video:duration>";
        }

        // Optional attributes
        if ($this->video->getInteractionCount()) {
            $result[] = "{$indenterSub}<video:view_count>" . $this->video->getInteractionCount() . "</video:view_count>";
        }

        $result[] = "{$indenter}</video:video>";

        return $result;
    }

    /**
     * Returns a SimpleXMLElement instance representing this element.
     *
     * @param null|SimpleXMLElement $parentElement
     *
     * @return \SimpleXMLElement|null
     */
    public function toXmlElement(SimpleXMLElement $parentElement = null)
    {
        // TODO: Implement toXmlElement() method.
    }
}
