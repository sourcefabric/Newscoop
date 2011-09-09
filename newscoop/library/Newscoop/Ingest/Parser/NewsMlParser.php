<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Ingest\Parser;

use Newscoop\Ingest\Parser;

/**
 * NewsML parser
 */
class NewsMlParser implements Parser
{
    /** @var SimpleXMLElement */
    private $xml;

    /**
     * @param string $content
     */
    public function __construct($content)
    {
        $this->xml = simplexml_load_file($content);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getString($this->xml->xpath('//HeadLine[1]'));
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        $content = array();
        foreach ($this->xml->xpath('//body.content/*') as $element) {
            $content[] = $element->asXML();
        }

        return implode("\n", $content) . "\n";
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return new \DateTime($this->getString($this->xml->xpath('//FirstCreated')));
    }

    /**
     * Get updated
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return new \DateTime($this->getString($this->xml->xpath('//ThisRevisionCreated')));
    }

    /**
     * Get priority
     *
     * @return int
     */
    public function getPriority()
    {
        $priority = array_shift($this->xml->xpath('//Priority'));
        return (int) $priority['FormalName'];
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        $service = array_shift($this->xml->xpath('//NewsService'));
        return (string) $service['FormalName'];
    }

    /**
     * Get public id
     *
     * @return string
     */
    public function getPublicId()
    {
        return $this->getString($this->xml->xpath('//PublicIdentifier'));
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->getString($this->xml->xpath('//p[@lede]'));
    }

    /**
     * Get string value of first matched element
     *
     * @param array $matches
     * @return string
     */
    private function getString(array $matches)
    {
        return (string) array_shift($matches);
    }
}
