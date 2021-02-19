<?php

namespace A3020\StructuredDataVideo;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;

class VideoPropertiesFactory implements ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * Create a Video Properties object for a concrete5 File.
     *
     * @param \Concrete\Core\Entity\File\File $file
     *
     * @return VideoProperties
     */
    public function makeForFile($file)
    {
        $fileVersion = $file->getApprovedVersion();

        // If not description is filled in, try to make something based off the title.
        $description = $fileVersion->getDescription();
        if (trim($description) === '') {
            /** @var \Concrete\Core\Utility\Service\Text $th */
            $th = $this->app->make('helper/text');
            $description = $th->unhandle($fileVersion->getTitle());
        }

        return (new VideoProperties())
            ->setName($fileVersion->getTitle())
            ->setDescription($description)
            ->setContentUrl($fileVersion->getURL())
            ->setUploadDate($fileVersion->getDateAdded())
            ->setDuration($fileVersion->getAttribute('duration'))
            ->setInteractionCount($fileVersion->getAttribute('view_count'));
    }
}
