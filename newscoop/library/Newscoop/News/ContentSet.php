<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * ContentSet
 * @EmbeddedDocument
 */
class ContentSet
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
    protected $inlineContent;

    /**
     * @EmbedMany(targetDocument="RemoteContent")
     * @var Doctrine\Common\Collections\Collection
     */
    protected $remoteContent;

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\ContentSet
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $contentSet = new self();
        if ($xml->inlineXML->count()) {
            $contentSet->setInlineContent($xml->inlineXML->html->body);
        } else if ($xml->remoteContent->count()) {
            $contentSet->setRemoteContent($xml);
        } else {
            throw new \InvalidArgumentException("Unknown content in " . $xml->asXML());
        }

        return $contentSet;
    }

    /**
     * Set remote content
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    private function setRemoteContent(\SimpleXMLElement $xml)
    {
        $this->remoteContent = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($xml->children() as $remoteContentXml) {
            $this->remoteContent->add(RemoteContent::createFromXml($remoteContentXml)); 
        }
    }

    /**
     * Get remote content
     *
     * @param string $rendition
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRemoteContent($rendition = null)
    {
        if ($rendition === null) {
            return $this->remoteContent;
        }

        foreach ($this->remoteContent as $remoteContent) {
            if ($remoteContent->getRendition() === $rendition) {
                return $remoteContent;
            }
        }

        return;
    }

    /**
     * Set inline content
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    private function setInlineContent(\SimpleXMLElement $xml)
    {
        $childrens = array();
        foreach ($xml->children() as $children) {
            $childrens[] = $children->asXML();
        }

        $this->inlineContent = implode("\n", $childrens);
    }

    /**
     * Get inline content
     *
     * @return string
     */
    public function getInlineContent()
    {
        return $this->inlineContent;
    }
}
