<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Ingest\Parser;

use Newscoop\Ingest\Parser;

/**
 * Infosperber parser
 */
class InfosperberParser implements Parser
{
    /** @var SimpleXMLElement */
    private $story;

    /** @var DateTime */
    private $date;

    /** @var string */
    private $subject;

    /**
     * @param string $content
     * @param string $subject
     */
    public function __construct($content, $subject = '')
    {
        if (is_object($content) && get_class($content) == 'SimpleXMLElement') {
            $this->story = $content;
        } else {
            $this->story = simplexml_load_file($content);
        }

        $this->subject = $subject;

        try {
            $dateString = $this->getString($this->story->xpath('pubDate'));
            $this->date = \DateTime::createFromFormat(DATE_RFC2822, $dateString);
        } catch (Exception $e) {
            $this->date = new \DateTime();
        }
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getString($this->story->xpath('title'));
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        $content = $this->getString($this->story->xpath('description'));

        return $content;
    }

    /**
     * Get created
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->date;
    }

    /**
     * Get updated
     *
     * @return DateTime
     */
    public function getUpdated()
    {
        return $this->date;
    }

    /**
     * Get date id yyyymmdd
     *
     * @return string
     */
    public function getDateId()
    {
        return $this->date->format('Ymd');
    }

    /**
     * Get news item id
     *
     * @return string
     */
    public function getNewsItemId()
    {
        return $this->getString($this->story->xpath('guid'));
    }

    public function getPriority()
    {
        return null;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return '';
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return '';
    }

    /**
     * Get lift embargo
     *
     * @return DateTime|null
     */
    public function getLiftEmbargo()
    {
        return null;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getService()
    {
        return 'infosperber';
    }

    public function getLanguage()
    {
        return 'de';
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    public function getCountry()
    {
        return '';
    }

    public function getProduct()
    {
        return 'infosperber';
    }

    public function getSubtitle()
    {
        return '';
    }

    public function getProviderId()
    {
        return '';
    }

    public function getRevisionId()
    {
        return '';
    }

    public function getLocation()
    {
        return '';
    }

     public function getProvider()
    {
        return 'infosperber';
    }

    public function getSource()
    {
        return 'infosperber';
    }

    public function getCatchLine()
    {
        return '';
    }

    public function getCatchWord()
    {
        return '';
    }

    public function getAuthors()
    {
        return trim(preg_replace("/\s+/mu", ' ', $this->getString($this->story->xpath('author'))));
    }

    public function getImages()
    {
        return null;
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

    /**
     * Extract individual stories from the XML feed
     */
    public function getStories($xml)
    {
        try {
            $xmlObj = simplexml_load_string($xml);
        } catch(\Exception $e) {
            throw new \Exception("Infosperber stories error {$e->getMessage()}");
        }

        if (!$xmlObj) {
            return array();
        }

        return $xmlObj->xpath('/rss/channel/item');
    }
}
