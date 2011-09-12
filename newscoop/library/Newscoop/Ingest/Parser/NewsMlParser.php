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
    /** @var string */
    private $xml_dir;

    /** @var array */
    private static $s_media_products = array('Photo Dienst D');
    /** @var array */
    private static $s_media_types = array('graphic', 'photo');
    /** @var array */
    private static $s_media_parts = array('main');
    /** @var array */
    private static $s_media_labels = array('caption');

    /** @var array */
    private static $s_lead_specs = array(
        array('key' => 'lede', 'value' => 'true'),
    );
    /** @var array */
    private static $s_unknown = array(
        'unknown',
    );

    /**
     * @param string $content
     */
    public function __construct($content)
    {
        $this->xml_file = basename($content);
        $this->xml_dir = dirname($content);
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
     * Is content element a lead one
     *
     * @return bool
     */
    private function isLead($element)
    {
        foreach (self::$s_lead_specs as $lead_spec) {
            if ( ((string) $element[$lead_spec['key']]) == $lead_spec['value'] ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent($p_withLead = true)
    {
        $content = array();

        foreach ($this->xml->xpath('//body.content/*') as $element) {
            if (!$p_withLead) {
                if ($this->isLead($element)) {
                    continue;
                }
            }

            $content[] = $element->asXML();
        }

        return implode("\n", $content) . "\n";
    }

    /**
     * Get lead (aka lede, per extensum)
     *
     * @return string
     */
    public function getLead()
    {
        $lead = '';

        foreach ($this->xml->xpath('//body.content/p') as $element) {
            if ($this->isLead($element)) {
                $lead = (string) $element;
                break;
            }
        }

        return $lead;
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
     * Get news versions
     *
     * @return array
     */
    public function getVersions()
    {
        $version_info = array_shift($this->xml->xpath('//NewsIdentifier/RevisionId'));
        $version_new = (string) $version_info;
        $version_old = (string) $version_info['PreviousRevision'];
        $version_update = (string) $version_info['Update'];

        return array(
            'new' => $version_new,
            'old' => $version_old,
            'update' => $version_update, 
        );
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
     * Get urgency
     *
     * @return int
     */
    public function getUrgency()
    {
        $urgency = array_shift($this->xml->xpath('//Urgency'));
        return (int) $urgency['FormalName'];
    }

    /**
     * Get links to other (supposedly image) newsml news
     *
     * Note that the link-ids do not (exactly) match the ids of the linked news!
     *      The link do not contain a suffix of the real id - it is usually ':1N'.
     *
     * @return array
     */
    public function getRelations()
    {
        $relations = array();

        foreach ($this->xml->xpath('//AssociatedWith') as $one_link) {
            $link_id = (string) $one_link['NewsItem'];
            $link_id_arr = explode(':', $link_id);
            $link_date = '';
            if (5 == count($link_id_arr)) {
                $link_date = $link_id_arr[3];
            }
            $found_files = glob($this->xml_dir . DIRECTORY_SEPARATOR . $link_date . '*xml*');
            if (!is_array($found_files)) {
                $found_files = array();
            }
            foreach ($found_files as $one_file) {
                $one_test_obj = new NewsMlParser($one_file);
                $one_test_obj_id = $one_test_obj->getPublicId();
                if (substr($one_test_obj_id, 0, strlen($link_id)) == $link_id) {
                    $relations[] = $one_test_obj;
                    break;
                }
            }
        }

        return $relations;
    }

    /**
     * Figures out whether the news is an image info
     *
     * Note this is a fuzzy matching. For ideal situations, it shall return either 0 or 1.
     *      If it returns a number from (0, 1), check what was changed at news structures!
     *
     * @return numeric
     */
    public function isImage() {
        $score = 0;

        if (in_array($this->getProduct(), self::$s_media_products)) {
            $score += 1;
        }
        foreach ($this->xml->xpath('//ContentItem/MediaType') as $one_part_type) {
            if ( in_array(strtolower($one_part_type['FormalName']), self::$s_media_types) ) {
                $score += 1;
                break;
            }
        }

        return ($score / 2);
    }

    /**
     * Get images
     *
     * @return array
     */
    public function getImages() {
        $images = array();
        $image_captions = array();

        foreach ($this->xml->xpath('//NewsComponent') as $one_part) {
            $one_ref_part = $one_part->ContentItem;
            $one_ref = (string) $one_ref_part['Href'];
            if (empty($one_ref)) {
                continue;
            }

            $one_role_part = $one_part->Role;
            $one_role = (string) $one_role_part['FormalName'];

            if (in_array(strtolower($one_role), self::$s_media_labels)) {
                $image_captions[$one_ref] = (string) ($one_part->ContentItem->DataContent);
                continue;
            }

            if (!in_array(strtolower($one_role), self::$s_media_parts)) {
                continue;
            }

            $one_type_part = $one_part->ContentItem->MediaType;
            $one_type = (string) $one_type_part['FormalName'];
            if ( !in_array(strtolower($one_type), self::$s_media_types) ) {
                continue;
            }

            $one_format_part = $one_part->ContentItem->Format;
            $one_format = (string) $one_format_part['FormalName'];

            $properties = array(
                'ref' => $one_ref,
                'format' => $one_format,
            );
            foreach ($one_part->ContentItem->Characteristics->Property as $one_prop) {
                $one_prop_key = (string) $one_prop['FormalName'];
                $one_prop_val = (string) $one_prop['Value'];
                $properties[strtolower($one_prop_key)] = $one_prop_val;
            }

            $images[] = $properties;
        }

        foreach ($images as $image_rank => $image_info) {
            if (array_key_exists($image_info['ref'], $image_captions)) {
                $images[$image_rank]['label'] = $image_captions[$image_info['ref']];
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
    public function getLocations()
    {
        return array(
            array(
                'type' => 'name',
                'value' => (string) $this->getString($this->xml->xpath('//DateLine[1]')),
            ),
        );
    }

    /**
     * Get keywords
     *
     * @return array
     */
    public function getKeywords()
    {
        $keywords = array();

        foreach ($this->xml->xpath('//NewsLine') as $one_catcher) {
            $one_catcher_part = $one_catcher->NewsLineType;
            if ('CatchWord' == ((string) $one_catcher_part['FormalName'])) {
                $one_catcher_value = (string) $one_catcher->NewsLineText;
                if (!empty($one_catcher_value)) {
                    $keywords[] = $one_catcher_value;
                }
            }
        }

        return $keywords;
    }

    /**
     * Get keylines
     *
     * @return array
     */
    public function getKeylines()
    {
        $keylines = array();

        foreach ($this->xml->xpath('//NewsLine') as $one_catcher) {
            $one_catcher_part = $one_catcher->NewsLineType;
            if ('CatchLine' == ((string) $one_catcher_part['FormalName'])) {
                $one_catcher_value = (string) $one_catcher->NewsLineText;
                if (!empty($one_catcher_value)) {
                    $keylines[] = $one_catcher_value;
                }
            }
        }

        return $keylines;
    }

    /**
     * Get sources
     *
     * @return array
     */
    public function getSources()
    {
        $sources = array();

        foreach ($this->xml->xpath('//Party') as $one_source) {
            $one_source_name = (string) $one_source['FormalName'];
            if (!in_array($one_source_name, $sources)) {
                $sources[] = $one_source_name;
            }
        }

        return $sources;
    }

    /**
     * Get list of authors
     *
     * @return array
     */
    public function getAuthors()
    {
        $authors = array();

        foreach ($this->xml->xpath('//Property[@FormalName="Author"]') as $one_property) {
            if ('author' == (string) $one_property['FormalName']) {
                $one_author = (string) $one_property['Value'];
                if (!in_array($one_author, $authors)) {
                    $authors[] = $one_author;
                }
            }
        }

        return $authors;
    }

    /**
     * Get a string of authors
     *
     * @return string
     */
    public function getByline($p_omitUnknown = false)
    {
        $byline  = (string) $this->getString($this->xml->xpath('//ByLine[1]'));
        if ($p_omitUnknown) {
            if (in_array($byline, self::$s_unknown)) {
                $byline = '';
            }
        }
        if (empty($byline)) {
            $byline = implode(', ', $this->getAuthors());
        }
        if ($p_omitUnknown) {
            if (in_array($byline, self::$s_unknown)) {
                $byline = '';
            }
        }

        return $byline;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
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
