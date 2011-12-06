<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * ItemRef
 * @EmbeddedDocument
 */
class ItemRef
{
    /**
     * @Id
     * @var string
     */
    protected $id;

    /**
     * @String
     * @var string
     */
    protected $residRef;

    /**
     * @String
     * @var string
     */
    protected $version;

    /**
     * @String
     * @var string
     */
    protected $contentType;

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
     * @String
     * @var string
     */
    protected $pubStatus;

    /**
     * @String
     * @var string
     */
    protected $slugline;

    /**
     * @String
     * @var string
     */
    protected $headline;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->residRef = (string) $xml['residref'];
        $this->version = (string) $xml['version'];
        $this->contentType = (string) $xml['contenttype'];
        $this->itemClass = (string) $xml->itemClass['qcode'];
        $this->provider = (string) $xml->provider['literal'];
        $this->versionCreated = new \DateTime((string) $xml->versionCreated);
        $this->pubStatus = (string) $xml->pubStatus['qcode'];
        $this->slugline = (string) $xml->slugline;
        $this->headline = (string) $xml->headline;
    }

    /**
     * Get resid ref
     *
     * @return string
     */
    public function getResidRef()
    {
        return $this->residRef;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Get content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
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
     * Get pub status
     *
     * @return string
     */
    public function getPubStatus()
    {
        return $this->pubStatus;
    }

    /**
     * Get slugline
     *
     * @return string
     */
    public function getSlugline()
    {
        return $this->slugline;
    }

    /**
     * Get headline
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }
}
