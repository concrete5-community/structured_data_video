<?php

namespace A3020\StructuredDataVideo;

use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Page\Page;

/**
 * Singleton helper object
 */
class VideoBlocks
{
    /**
     * @var Connection
     */
    private $connection;

    /** @var int[] */
    protected $blockIds;

    /** @var int[] */
    protected $pagesWithVideos;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get all block ids of the Video block type.
     *
     * @return int[]
     */
    public function getBlockIds()
    {
        if ($this->blockIds !== null) {
            return $this->blockIds;
        }

        $bt = BlockType::getByHandle('video');
        if (!$bt) {
            return [];
        }

        $blockIds = $this->connection->fetchAll('
            SELECT bID FROM Blocks WHERE btID = ?
        ', [ $bt->getBlockTypeID()]);

        // Only get the bID column. Make them integers.
        $blockIds = array_column($blockIds, 'bID');
        $blockIds = array_map('intval', $blockIds);

        $this->blockIds = $blockIds;

        return $this->blockIds;
    }

    /**
     * Get all Video block ids for a specific page.
     *
     * @param Page $page
     *
     * @return int[]
     */
    public function videoBlocksForPage(Page $page)
    {
        if (!in_array((int) $page->getCollectionID(), $this->getPagesWithVideos())) {
            return [];
        }
        
        $blockIds = $this->connection->fetchAll('
            SELECT bID FROM CollectionVersionBlocks 
            WHERE cID = ? AND cvID = ? AND bID IN (?)
        ', [
            $page->getCollectionID(),
            $page->getVersionObject()->getVersionID(),
            implode(',', $this->getBlockIds()),
        ]);

        $blockIds = array_column($blockIds, 'bID');

        return array_map('intval', $blockIds);
    }

    /**
     * Returns which pages actually have a Video block on it.
     *
     * By using this, we don't have to execute a query per page.
     *
     * @return int[]
     */
    private function getPagesWithVideos()
    {
        if ($this->pagesWithVideos !== null) {
            return $this->pagesWithVideos;
        }

        $pageIds = $this->connection->fetchAll('
            SELECT DISTINCT(cID) FROM CollectionVersionBlocks 
            WHERE bID IN (?)
        ', [
            implode(',', $this->getBlockIds()),
        ]);

        $pageIds = array_column($pageIds, 'cID');

        $this->pagesWithVideos = array_map('intval', $pageIds);

        return $this->pagesWithVideos;
    }
}
