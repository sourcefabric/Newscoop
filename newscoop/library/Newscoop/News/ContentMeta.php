<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * ContentMeta
 * @EmbeddedDocument
 */
class ContentMeta
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
    protected $urgency;

    /**
     * @String
     * @var string
     */
    protected $slugline;

    /**
     * @String
     * @var string
     */
    protected $headline;

    /**
     * @String
     * @var string
     */
    protected $dateline;

    /**
     * @String
     * @var string
     */
    protected $by;

    /**
     * @String
     * @var string
     */
    protected $creditline;

    /**
     * @String
     * @var string
     */
    protected $description;

    /**
     * @EmbedMany(targetDocument="Subject")
     * @var Doctrine\Common\Collections\Collection
     */
    protected $subjects;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->urgency = (string) $xml->urgency;
        $this->slugline = (string) $xml->slugline;
        $this->headline = (string) $xml->headline;
        $this->dateline = (string) $xml->dateline;
        $this->creditline = (string) $xml->creditline;
        $this->by = (string) $xml->by;
        $this->description = (string) $xml->description;
        $this->setSubjects($xml);
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
     * Get by
     *
     * @return string
     */
    public function getBy()
    {
        return $this->by;
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
            $this->subjects->add(new Subject($subjectXml));
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
}
