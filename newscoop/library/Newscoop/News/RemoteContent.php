<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * RemoteContent
 * @EmbeddedDocument
 */
class RemoteContent
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
    protected $residref;

    /**
     * @String
     * @var string
     */
    protected $href;

    /**
     * @Int
     * @var int
     */
    protected $size;

    /**
     * @String
     * @var string
     */
    protected $rendition;

    /**
     * @String
     * @var string
     */
    protected $contentType;

    /**
     * @String
     * @var string
     */
    protected $format;

    /**
     * @String
     * @var string
     */
    protected $generator;

    /**
     * @Int
     * @var int
     */
    protected $width;

    /**
     * @Int
     * @var int
     */
    protected $height;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->residref = (string) $xml['residref'];
        $this->href = (string) $xml['href'];
        $this->size = (int) $xml['size'];
        $this->rendition = (string) $xml['rendition'];
        $this->contentType = (string) $xml['contenttype'];
        $this->format = (string) $xml['format'];
        $this->generator = (string) $xml['generator'];
        $this->width = (int) $xml['width'];
        $this->height = (int) $xml['height'];
    }

    /**
     * Get residref
     *
     * @return string
     */
    public function getResidref()
    {
        return $this->residref;
    }

    /**
     * Get href
     *
     * @return string
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     * Get size
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get rendition
     *
     * @return string
     */
    public function getRendition()
    {
        return $this->rendition;
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
     * Get format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Get generator
     *
     * @return string
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Get width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
