<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * ItemMeta
 * @EmbeddedDocument
 */
class ItemMeta
{
    const STATUS_USABLE = 'stat:usable';
    const STATUS_WITHHELD = 'stat:withheld';
    const STATUS_CANCELED = 'stat:canceled';

    /**
     * @Id
     * @var string
     */
    protected $id;

    /**
     * @String
     * @var string
     */
    protected $itemClass;

    /**
     * @String
     * @var string
     */
    protected $provider;

    /**
     * @Date
     * @var DateTime
     */
    protected $versionCreated;

    /**
     * @Date
     * @var DateTime
     */
    protected $firstCreated;

    /**
     * @String
     * @var string
     */
    protected $pubStatus;

    /**
     * @String
     * @var string
     */
    protected $role;

    /**
     * @String
     * @var string
     */
    protected $title;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->itemClass = (string) $xml->itemClass['qcode'];
        $this->provider = (string) $xml->provider['literal'];
        $this->versionCreated = new \DateTime((string) $xml->versionCreated);
        $this->firstCreated = new \DateTime((string) $xml->firstCreated);
        $this->pubStatus = (string) $xml->pubStatus['qcode'] ?: self::STATUS_USABLE;
        $this->role = (string) $xml->role['qcode'];
        $this->title = (string) $xml->title;
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
