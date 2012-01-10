<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * PackageItem
 * @ODM\Document
 */
class PackageItem extends Item
{
    /**
     * @ODM\EmbedOne(targetDocument="GroupSet")
     * @var Newscoop\News\GroupSet
     */
    protected $groupSet;

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\PackageItem
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $item = parent::createFromXml($xml);
        $item->groupSet = GroupSet::createFromXml($xml->groupSet);
        return $item;
    }

    /**
     * Get group set
     *
     * @return Newscoop\News\GroupSet
     */
    public function getGroupSet()
    {
        return $this->groupSet;
    }
}
