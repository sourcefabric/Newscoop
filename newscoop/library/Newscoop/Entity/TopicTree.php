<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

/**
 * @Entity
 * @Table(name="Topics")
 */
class TopicTree
{
    /**
     * @Id
     * @Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $node_left;

    /**
     * @Column(type="integer")
     * @var int
     */
    private $node_right;

    /**
     * @OneToMany(targetEntity="Newscoop\Entity\Topic", mappedBy="topic", cascade={"persist", "remove"}, indexBy="language")
     * @var Doctrine\Common\Collections\Collection
     */
    private $names;

    /**
     * @param int $id
     * @param int $left
     */
    public function __construct($id, $left)
    {
        $this->id = (int) $id;
        $this->node_left = (int) $left;
        $this->node_right = $this->node_left + 1;
        $this->names = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get topic id
     *
     * proxy to getId method
     *
     * @return int
     */
    public function getTopicId()
    {
        return $this->getId();
    }

    /**
     * Get left
     *
     * @return int
     */
    public function getLeft()
    {
        return $this->node_left;
    }

    /**
     * Set right
     *
     * @param int $right
     * @return void
     */
    public function setRight($right)
    {
        $this->node_right = (int) $right;
    }

    /**
     * Get right
     *
     * @return int
     */
    public function getRight()
    {
        return $this->node_right;
    }

    /**
     * Get name
     *
     * @param Newscoop\Entity\Language $language
     * @return string
     */
    public function getName(Language $language = null)
    {
        if ($language === null) {
            return $this->names->isEmpty() ? null : $this->names->first()->current()->getName();
        }

        foreach ($this->names as $name) {
            if ($name->getLanguageId() === $language->getId()) {
                return (string) $name;
            }
        }

        return null;
    }

    /**
     * Add name
     *
     * @param string $name
     * @param Newscoop\Entity\Language $language
     * @return void
     */
    public function addName($name, Language $language)
    {
        $topicName = new Topic($this, $language, $name);
        if (!$this->names->contains($topicName)) {
            $this->names->add($topicName);
        }
    }
}
