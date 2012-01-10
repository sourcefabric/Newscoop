<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Subject
 * @ODM\EmbeddedDocument
 */
class Subject
{
    /**
     * @ODM\Id
     * @var string
     */
    protected $id;

    /**
     * @ODM\String
     * @var string
     */
    protected $qcode;

    /**
     * @ODM\String
     * @var string
     */
    protected $type;

    /**
     * @ODM\String
     * @var string
     */
    protected $name;

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\Subject
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $subject = new self();
        $subject->qcode = isset($xml['qcode']) ? (string) $xml['qcode'] : null;
        $subject->type = isset($xml['type']) ? (string) $xml['type'] : null;
        $subject->name = $xml->name ? (string) $xml->name : (!empty($xml['literal']) ? (string) $xml['literal'] : null);
        return $subject;
    }

    /**
     * Get QCode
     *
     * @return string
     */
    public function getQCode()
    {
        return $this->qcode;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
