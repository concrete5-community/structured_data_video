<?php

namespace A3020\StructuredDataVideo;

use Carbon\CarbonInterval;
use Concrete\Core\Support\Facade\Application;
use DateInterval;
use DateTime;

class VideoProperties
{
    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var array */
    private $thumbnails;

    /** @var \DateTimeInterface uploadDate */
    private $uploadDate;

    /** @var string */
    private $duration;

    /** @var string */
    private $contentUrl;

    /** @var int */
    private $interactionCount;

    /**
     * @return array
     */
    public function toArray()
    {
        $properties = [
            'name' => $this->getName(),
            'description' => $this->getShortenedDescription(),
            'thumbnailUrl' => $this->getThumbnails(),
            'uploadDate' => $this->getUploadDateTransformed(),
            'duration' => $this->getDurationInIso(),
            'contentUrl' => $this->getContentUrl(),
            'interactionCount' => $this->getInteractionCount(),
        ];

        return array_filter($properties);
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string|null
     */
    public function getShortenedDescription()
    {
        // Google allows max 2048 chars in the description.
        if (mb_strlen($this->description) <= 2048) {
            return $this->description;
        }

        $app = Application::getFacadeApplication();

        return $app->make('helper/text')->shorten($this->description, 2048 - 3);
    }

    /**
     * @param string $description
     *
     * @return static
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getThumbnails()
    {
        return $this->thumbnails;
    }

    /**
     * @return string|null
     */
    public function getFirstThumbnail()
    {
        return $this->thumbnails ? $this->thumbnails[0] : null;
    }

    /**
     * @param string $thumbnail
     *
     * @return static
     */
    public function addThumbnail($thumbnail)
    {
        $this->thumbnails[] = $thumbnail;

        return $this;
    }

    /**
     * @param array $thumbnails
     *
     * @return static
     */
    public function setThumbnails(array $thumbnails)
    {
        $this->thumbnails = $thumbnails;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUploadDate()
    {
        return $this->uploadDate;
    }

    public function getUploadDateTransformed()
    {
        return $this->uploadDate->format('c');
    }

    /**
     * @param \DateTimeInterface $uploadDate
     *
     * @return static
     */
    public function setUploadDate(\DateTimeInterface $uploadDate)
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration In seconds
     *
     * @return static
     */
    public function setDuration($duration)
    {
        $this->duration = (int) $duration;

        return $this;
    }

    /**
     * Get duration in ISO 8601 format.
     *
     * Example output: PT2M30S (2 minutes, 30 seconds).
     *
     * @see https://en.wikipedia.org/wiki/ISO_8601
     * @see https://developers.google.com/search/docs/data-types/video
     *
     * @return string|null
     *
     * @throws \Exception
     */
    private function getDurationInIso()
    {
        if (!$this->getDuration()) {
            return null;
        }

        $d1 = new DateTime();
        $d2 = new DateTime();
        $d2->add(new DateInterval('PT'.$this->getDuration().'S'));

        $interval = $d2->diff($d1);

        return CarbonInterval::getDateIntervalSpec($interval);
    }

    /**
     * @return string|null
     */
    public function getContentUrl()
    {
        return $this->contentUrl;
    }

    /**
     * @param string $contentUrl
     *
     * @return static
     */
    public function setContentUrl($contentUrl)
    {
        $this->contentUrl = $contentUrl;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getInteractionCount()
    {
        return $this->interactionCount;
    }

    /**
     * @param int $interactionCount
     *
     * @return static
     */
    public function setInteractionCount($interactionCount)
    {
        if ((int) $interactionCount) {
            $this->interactionCount = (int) $interactionCount;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->getName());
    }

    /**
     * Google requires certain attributes to be set.
     *
     * Return false if one of those required attributes is missing or empty.
     *
     * @see https://developers.google.com/search/docs/data-types/video
     * @see https://support.google.com/webmasters/answer/80471?hl=en
     *
     * @return bool
     */
    public function isComplete()
    {
        return
            // Name is required.
            !empty(trim($this->getName()))

            // Description is required.
            && !empty(trim($this->getDescription()))

            // A poster / thumbnail is required.
            && $this->getFirstThumbnail()

            // A link to the file is required for sitemap, not necessarily for structured data.
            && $this->getContentUrl();
    }
}
