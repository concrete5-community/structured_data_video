<?php

namespace A3020\StructuredDataVideo;

/**
 * @see https://developers.google.com/search/docs/data-types/video
 */
class StructuredData
{
    /**
     * @var VideoProperties
     */
    private $videoProperties;

    public function __construct()
    {
        $this->videoProperties = new VideoProperties();
    }

    /**
     * @param VideoProperties $videoProperties
     */
    public function setVideoProperties(VideoProperties $videoProperties)
    {
        $this->videoProperties = $videoProperties;
    }

    /**
     * @return bool
     */
    public function isComplete()
    {
        return $this->videoProperties->isComplete();
    }

    /**
     * @return string
     */
    public function toJson()
    {
        $defaultProperties = [
            '@context' => 'http://schema.org',
            '@type' => 'VideoObject',
        ];

        return json_encode(
            array_merge($defaultProperties, $this->videoProperties->toArray())
        );
    }
}
