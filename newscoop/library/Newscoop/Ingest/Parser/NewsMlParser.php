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
    const MEDIA_PRODUCT = 'Photo Dienst D';

    /** @var SimpleXMLElement */
    protected $xml;

    /** @var string */
    protected $dir;

    /**
     * @param string $content
     */
    public function __construct($content)
    {
        $this->xml = simplexml_load_file($content);
        $this->dir = dirname($content);
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getString($this->xml->xpath('//HeadLine'));
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        $content = array();
        foreach ($this->xml->xpath('//body.content/*[not(@lede)]') as $element) {
            $content[] = $element->asXML();
        }

        return str_replace('hl2>', 'h2>', implode("\n", $content));
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return new \DateTime($this->getString($this->xml->xpath('//FirstCreated')));
    }

    /**
     * Get updated
     *
     * @return \DateTime
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
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->getString($this->xml->xpath('//p[@lede="true"]'));
    }

    /**
     * Get news product
     *
     * @return string
     */
    public function getProduct()
    {
        $product = array_shift($this->xml->xpath('//NewsProduct'));
        return (string) $product['FormalName'];
    }

    /**
     * Get news item type
     *
     * @return string
     */
    public function getType()
    {
        $type_info = array_shift($this->xml->xpath('//NewsItemType'));
        return (string) $type_info['FormalName'];
    }

    /**
     * Test if is image 
     *
     * @return bool
     */
    public function isImage()
    {
        return $this->getProduct() == self::MEDIA_PRODUCT;
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages()
    {
        $images = array();
        foreach ($this->xml->xpath('//NewsManagement/AssociatedWith') as $assoc) {
            list(,,,$dateId, $newsItemId) = explode(':', (string) $assoc['NewsItem']);
            foreach (glob("{$this->dir}/{$dateId}*_{$newsItemId}.xml") as $imageNewsMl) {
                $images[] = new self($imageNewsMl);
            }
        }

        return $images;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        $status = array_shift($this->xml->xpath('//Status'));
        return (string) $status['FormalName'];
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->getString($this->xml->xpath('//NewsLines/DateLine'));
    }

    /**
     * Get list of authors
     *
     * @return string
     */
    public function getAuthors()
    {
        $authors = array();
        foreach ($this->xml->xpath('//AdministrativeMetadata/Property[@FormalName="author"]') as $author) {
            $authors[] = (string) $author['Value'];
        }

        return implode(', ', $authors);
    }

    /**
     * Get provider
     *
     * @return string
     */
    public function getProvider()
    {
        $provider = array_shift($this->xml->xpath('//AdministrativeMetadata/Provider/Party'));
        return (string) $provider['FormalName'];
    }

    /**
     * Get provider id
     *
     * @return string
     */
    public function getProviderId()
    {
        return $this->getString($this->xml->xpath('//Identification/NewsIdentifier/ProviderId'));
    }

    /**
     * Get date id
     *
     * @return string
     */
    public function getDateId()
    {
        return $this->getString($this->xml->xpath('//Identification/NewsIdentifier/DateId'));
    }

    /**
     * Get news item id
     *
     * @return string
     */
    public function getNewsItemId()
    {
        return $this->getString($this->xml->xpath('//Identification/NewsIdentifier/NewsItemId'));
    }

    /**
     * Get revision id
     *
     * @return int
     */
    public function getRevisionId()
    {
        return (int) $this->getString($this->xml->xpath('//Identification/NewsIdentifier/RevisionId'));
    }

    /**
     * Get instruction
     *
     * @return string
     */
    public function getInstruction()
    {
        $instruction = array_shift($this->xml->xpath('//NewsManagement/Instruction'));
        return (string) $instruction['FormalName'];
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        $language = array_shift($this->xml->xpath('//DescriptiveMetadata/Language'));
        return strtolower($language['FormalName']);
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        $subject = array_shift($this->xml->xpath('//DescriptiveMetadata/SubjectCode/Subject'));
        return (string) $subject['FormalName'];
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        $country = array_shift($this->xml->xpath('//DescriptiveMetadata/Location/Property[@FormalName="Country"]'));
        return (string) $country['Value'];
    }

    /**
     * Get source
     *
     * @return string
     */
    public function getSource()
    {
        $sources = array();
        foreach ($this->xml->xpath('//AdministrativeMetadata/Source/Party') as $party) {
            $sources[] = (string) $party['FormalName'];
        }

        return implode(', ', $sources);
    }

    /**
     * Get catch line
     *
     * @return string
     */
    public function getCatchLine()
    {
        $catchLine = $this->xml->xpath('//NewsLines/NewsLine/NewsLineType[@FormalName="CatchLine"]');
        return empty($catchLine) ? '' : $this->getString(array_shift($catchLine)->xpath('following::NewsLineText'));
    }

    /**
     * Get catch word
     *
     * @return string
     */
    public function getCatchWord()
    {
        $catchWord = $this->xml->xpath('//NewsLines/NewsLine/NewsLineType[@FormalName="CatchWord"]');
        return $this->getString(array_shift($catchWord)->xpath('following::NewsLineText'));
    }

    /**
     * Get sub title
     *
     * @return string
     */
    public function getSubTitle()
    {
        return $this->getString($this->xml->xpath('//NewsLines/SubHeadLine'));
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        $contentItem = array_shift($this->xml->xpath('//NewsComponent/ContentItem[@Href]'));
        $href = (string) $contentItem['Href'];
        return "$this->dir/$href";
    }

    /**
     * Get lift embargo
     *
     * @return \DateTime|null
     */
    public function getLiftEmbargo()
    {
        $datetime = array_shift($this->xml->xpath('//StatusWillChange/DateAndTime'));
        if ((string) $datetime !== '') {
            return new \DateTime((string) $datetime);
        }
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
