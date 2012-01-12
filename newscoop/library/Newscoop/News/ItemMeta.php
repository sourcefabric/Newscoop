<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ItemMeta
 * @ODM\EmbeddedDocument
 */
class ItemMeta
{
    const STATUS_USABLE = 'stat:usable';
    const STATUS_WITHHELD = 'stat:withheld';
    const STATUS_CANCELED = 'stat:canceled';

    const CLASS_TEXT = 'icls:text';
    const CLASS_PICTURE = 'icls:picture';
    const CLASS_PACKAGE = 'icls:composite';

    /**
     * @ODM\Id
     * @var string
     */
    protected $id;

    /**
     * @ODM\String
     * @var string
     */
    protected $itemClass;

    /**
     * @ODM\String
     * @var string
     */
    protected $provider;

    /**
     * @ODM\Date
     * @var DateTime
     */
    protected $versionCreated;

    /**
     * @ODM\Date
     * @var DateTime
     */
    protected $firstCreated;

    /**
     * @ODM\String
     * @var string
     */
    protected $pubStatus;

    /**
     * @ODM\String
     * @var string
     */
    protected $role;

    /**
     * @ODM\String
     * @var string
     */
    protected $title;

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\ItemMeta
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $meta = new static();
        $meta->itemClass = (string) $xml->itemClass['qcode'];
        $meta->provider = (string) $xml->provider['literal'];
        $meta->versionCreated = new \DateTime((string) $xml->versionCreated);
        $meta->firstCreated = new \DateTime((string) $xml->firstCreated);
        $meta->pubStatus = (string) $xml->pubStatus['qcode'] ?: self::STATUS_USABLE;
        $meta->role = (string) $xml->role['qcode'];
        $meta->title = (string) $xml->title;
        return $meta;
    }

    /**
     * Get item class
     *
     * @return string
     */
    public function getItemClass()
    {
        return $this->itemClass;
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * Get version created
     *
     * @return DateTime
     */
    public function getVersionCreated()
    {
        return $this->versionCreated;
    }

    /**
     * Get first created
     *
     * @return DateTime
     */
    public function getFirstCreated()
    {
        return $this->firstCreated;
    }

    /**
     * Set pub status
     *
     * @param string $status
     * @return void
     */
    public function setPubStatus($pubStatus)
    {
        $this->pubStatus = (string) $pubStatus;
    }

    /**
     * Get pub status
     *
     * @return string
     */
    public function getPubStatus()
    {
        return $this->pubStatus;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
