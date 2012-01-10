<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * ContentMeta
 * @ODM\EmbeddedDocument
 */
class ContentMeta
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
    protected $urgency;

    /**
     * @ODM\String
     * @var string
     */
    protected $slugline;

    /**
     * @ODM\String
     * @var string
     */
    protected $headline;

    /**
     * @ODM\String
     * @var string
     */
    protected $dateline;

    /**
     * @ODM\String
     * @ODM\AlsoLoad("by")
     * @var string
     */
    protected $byline;

    /**
     * @ODM\String
     * @var string
     */
    protected $creditline;

    /**
     * @ODM\String
     * @var string
     */
    protected $description;

    /**
     * @ODM\EmbedMany(targetDocument="Subject")
     * @var Doctrine\Common\Collections\Collection
     */
    protected $subjects;

    /**
     * @ODM\String
     * @var string
     */
    protected $language;

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\ContentMeta
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $meta = new self();
        $meta->urgency = (string) $xml->urgency;
        $meta->slugline = (string) $xml->slugline;
        $meta->headline = (string) $xml->headline;
        $meta->dateline = (string) $xml->dateline;
        $meta->creditline = (string) $xml->creditline;
        $meta->byline = (string) $xml->by;
        $meta->description = (string) $xml->description;
        $meta->setSubjects($xml);
        $meta->language = (string) $xml->language['tag'];
        return $meta;
    }

    /**
     * Get urgency
     *
     * @return string
     */
    public function getUrgency()
    {
        return $this->urgency;
    }

    /**
     * Get slugline
     *
     * @return string
     */
    public function getSlugline()
    {
        return $this->slugline;
    }

    /**
     * Get headline
     *
     * @return string
     */
    public function getHeadline()
    {
        return $this->headline;
    }

    /**
     * Get dateline
     *
     * @return string
     */
    public function getDateline()
    {
        return $this->dateline;
    }

    /**
     * Get byline
     *
     * @return string
     */
    public function getByline()
    {
        return $this->byline;
    }

    /**
     * Get creditline
     *
     * @return string
     */
    public function getCreditline()
    {
        return $this->creditline;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set subjects
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    protected function setSubjects(\SimpleXMLElement $xml)
    {
        $this->subjects = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($xml->subject as $subjectXml) {
            $this->subjects->add(Subject::createFromXml($subjectXml));
        }
    }

    /**
     * Get subjects
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Get subject by given code
     *
     * @param string $code
     * @return Newscoop\News\Subject
     */
    public function getSubject($code)
    {
        foreach ($this->subjects as $subject) {
            if ($subject->getQCode() === $code || $subject->getType() === $code) {
                return $subject;
            }
        }
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
