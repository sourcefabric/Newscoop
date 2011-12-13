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
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\RemoteContent
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $content = new self();
        $content->residref = (string) $xml['residref'];
        $content->href = (string) $xml['href'];
        $content->size = (int) $xml['size'];
        $content->rendition = (string) $xml['rendition'];
        $content->contentType = (string) $xml['contenttype'];
        $content->format = (string) $xml['format'];
        $content->generator = (string) $xml['generator'];
        $content->width = (int) $xml['width'];
        $content->height = (int) $xml['height'];
        return $content;
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
