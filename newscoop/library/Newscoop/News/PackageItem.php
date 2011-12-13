<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * PackageItem
 * @Document
 */
class PackageItem extends Item
{
    /**
     * @EmbedOne(targetDocument="GroupSet")
     * @var Newscoop\News\GroupSet
     */
    protected $groupSet;

    /**
     * @param string $id
     * @param int $version
     */
    public function __construct($id, $version = 1)
    {
        parent::__construct($id, $version);
    }

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
