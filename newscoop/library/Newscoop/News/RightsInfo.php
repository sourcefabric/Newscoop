<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * RightsInfo
 * @ODM\EmbeddedDocument
 */
class RightsInfo
{
    /**
     * @ODM\Id
     * @var string
     */
    protected $id;

    /**
     * @ODM\String
     * @var string
     */
    protected $copyrightHolder;

    /**
     * @ODM\String
     * @var string
     */
    protected $copyrightNotice;

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\RightsInfo
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $info = new self();
        $info->copyrightHolder = (string) $xml->copyrightHolder['literal'];
        $info->copyrightNotice = (string) $xml->copyrightNotice;
        return $info;
    }

    /**
     * Get copyright holder
     *
     * @return string
     */
    public function getCopyrightHolder()
    {
        return $this->copyrightHolder;
    }

    /**
     * Get copyright notice
     *
     * @return string
     */
    public function getCopyrightNotice()
    {
        return $this->copyrightNotice;
    }
}
