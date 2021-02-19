<?php

namespace A3020\StructuredDataVideo\Page\Sitemap\Element;

use A3020\StructuredDataVideo\VideoBlocks;
use Concrete\Core\Support\Facade\Application;

class SitemapHeader extends \Concrete\Core\Page\Sitemap\Element\SitemapHeader
{
    /**
     * @param bool $selfClosing
     *
     * @return string
     */
    protected function getUrlset($selfClosing)
    {
        $result = '<urlset xmlns="' . static::DEFAULT_NAMESPACE . '"';
        if ($this->isIsMultilingual()) {
            $result .= ' xmlns:' . static::MULTILINGUAL_NAMESPACE_NAME . '="' . static::MULTILINGUAL_NAMESPACE . '"';
        }

        $this->addVideoNamespace($result);

        $result .= $selfClosing ? '/>' : '>';

        return $result;
    }

    /**
     * @param string $result
     */
    private function addVideoNamespace(&$result)
    {
        $app = Application::getFacadeApplication();

        /** @var VideoBlocks $videoBlocks */
        $videoBlocks = $app->make(VideoBlocks::class);
        if ($videoBlocks->getBlockIds()) {
            $result .= ' xmlns:video="http://www.google.com/schemas/sitemap-video/1.1"';
        }
    }
}
