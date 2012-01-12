<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Test feed
 * @ODM\Document
 */
class TestFeed extends Feed
{
    public function update(\Doctrine\Common\Persistence\ObjectManager $om, ItemService $itemService)
    {
        $this->updated = new \DateTime();
    }

    public function getName()
    {
        return 'TestFeed';
    }

    public function getRemoteContentSrc(RemoteContent $remoteContent)
    {
        return APPLICATION_PATH . '/../tests/fixtures/picture.jpg';
    }

    public function getItem($id)
    {
        $path = APPLICATION_PATH . "/{$id}.xml";
        if (!file_exists($path)) {
            return null;
        }

        $xml = simplexml_load_file($path);
        return NewsItem::createFromXml($xml->contentSet->newItem);
    }
}
