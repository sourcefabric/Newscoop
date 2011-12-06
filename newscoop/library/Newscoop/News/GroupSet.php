<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\News;

/**
 * GroupSet
 * @EmbeddedDocument
 */
class GroupSet
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
    protected $root;

    /**
     * @EmbedMany(targetDocument="Group")
     * @var Doctrine\Common\Collections\Collection
     */
    protected $groups;

    /**
     * @param SimpleXMLElement $xml
     */
    public function __construct(\SimpleXMLElement $xml)
    {
        $this->root = (string) $xml['root'];
        $this->setGroups($xml);
    }

    /**
     * Set groups
     *
     * @param SimpleXMLElement $xml
     * @return void
     */
    private function setGroups(\SimpleXMLElement $xml)
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($xml->children() as $groupXml) {
            $this->groups->add(new Group($groupXml));
        }
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Get root group
     *
     * @return Newscoop\News\Group
     */
    public function getRootGroup()
    {
        foreach ($this->groups as $group) {
            if ($group->getId() === $this->root) {
                return $group;
            }
        }

        return;
    }

    /**
     * Get group by given group reference
     *
     * @param Newscoop\News\GroupRef $ref
     * @return Newscoop\News\Group
     */
    public function getGroup(GroupRef $ref)
    {
        foreach ($this->groups as $group) {
            if ($group->getId() === $ref->getIdref()) {
                return $group;
            }
        }

        return;
    }
}
