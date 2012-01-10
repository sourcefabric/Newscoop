<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Group
 * @ODM\EmbeddedDocument
 */
class Group
{
    /**
     * @ODM\Id(strategy="NONE")
     * @var string
     */
    protected $id;

    /**
     * @ODM\String
     * @var string
     */
    protected $role;

    /**
     * @ODM\String
     * @var string
     */
    protected $mode;

    /**
     * @ODM\EmbedMany(
     *   discriminatorMap={
     *     "group"="GroupRef",
     *     "item"="ItemRef"
     *   })
     * @var Doctrine\Common\Collections\Collection
     */
    protected $refs;

    /**
     * @param string $id
     */
    public function __construct($id)
    {
        $this->id = (string) $id;
    }

    /**
     * Factory
     *
     * @param SimpleXMLElement $xml
     * @return Newscoop\News\Group
     */
    public static function createFromXml(\SimpleXMLElement $xml)
    {
        $group = new self($xml['id']);
        $group->role = (string) $xml['role'];
        $group->mode = (string) $xml['mode'];
        $group->setRefs($xml);
        return $group;
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
     * Set refs
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    public function setRefs($xml)
    {
        $this->refs = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($xml->children() as $refXml) {
            if ($refXml->getName() === 'groupRef') {
                $this->refs->add(new GroupRef($refXml['idref']));
            } else if ($refXml->getName() === 'itemRef') {
                $this->refs->add(ItemRef::createFromXml($refXml));
            } else {
                throw new \InvalidArgumentException("Expected group or item ref, got '{$refXml->getName()}'");
            }
        }
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
