<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Ingest\Parser;

/**
 * NewsML parser
 */
class NewsMlParser
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
        return (string) array_shift($this->xml->xpath('//NewsItem/NewsComponent/NewsLines/HeadLine'));
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        $content = array();
        foreach ($this->xml->xpath('//NewsItem/NewsComponent/NewsComponent/ContentItem/DataContent/nitf/body/body.content/*') as $element) {
            $content[] = $element->asXML();
        }

        return implode("\n", $content) . "\n";
    }
}
