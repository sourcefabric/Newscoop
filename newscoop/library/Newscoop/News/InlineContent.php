<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * InlineContent
 * @EmbeddedDocument
 */
class InlineContent
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
    protected $contentType;

    /**
     * @Int
     * @var int
     */
    protected $wordCount;

    /**
     * @String
     * @var string
     */
    protected $content;

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\InlineContent
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $content = new self();
        $content->contentType = (string) $xml['contenttype'];
        $content->wordCount = (int) $xml['wordcount'];
        $content->content = $xml->children()->asXML();
        return $content;
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
     * Get word count
     *
     * @return int
     */
    public function getWordCount()
    {
        return $this->wordCount;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        switch ($this->contentType) {
            case 'application/xhtml+html':
                return $this->getContentBody();
                break;
        }
    }

    /**
     * Get content body
     *
     * @return string
     */
    private function getContentBody()
    {
        $elements = array();
        $xml = simplexml_load_string($this->content);
        foreach ($xml->body->children() as $element) {
            $elements[] = $element->asXML();
        }

        return implode("\n", $elements);
    }
}
