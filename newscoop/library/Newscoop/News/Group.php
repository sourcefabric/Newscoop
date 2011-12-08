<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * Group
 * @EmbeddedDocument
 */
class Group
{
    /**
     * @Id(strategy="NONE")
     * @var string
     */
    protected $id;

    /**
     * @String
     * @var string
     */
    protected $role;

    /**
     * @String
     * @var string
     */
    protected $mode;

    /**
     * @EmbedMany(
     *   discriminatorMap={
     *     "group"="GroupRef",
     *     "item"="ItemRef"
     *   })
     * @var Doctrine\Common\Collections\Collection
     */
    protected $refs;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->id = (string) $xml['id'];
        $this->role = (string) $xml['role'];
        $this->refs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->mode = (string) $xml['mode'];
        foreach ($xml->children() as $refXml) {
            if ($refXml->getName() === 'groupRef') {
                $this->refs->add(new GroupRef($refXml));
            } else if ($refXml->getName() === 'itemRef') {
                $this->refs->add(new ItemRef($refXml));
            } else {
                throw new \InvalidArgumentException("Expected group or item ref, got '{$refXml->getName()}'");
            }
        }
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Get refs
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getRefs()
    {
        return $this->refs;
    }
}
