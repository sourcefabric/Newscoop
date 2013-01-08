<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Newscoop\View\TopicView;

/**
 * @Entity
 * @Table(name="TopicNames")
 */
class Topic
{
    /**
     * @Id
     * @Column(type="integer", name="fk_topic_id")
     * @var int
     * @todo add reference to topic
     */
    private $id;

    /**
     * @Id
     * @Column(type="integer", name="fk_language_id")
     * @var int
     * @todo add reference to language
     */
    private $language;

    /**
     * @Column(type="string", length="255")
     * @var string
     */
    private $name;

    /**
     * @param int $id
     * @param int $language
     * @param int $name
     */
    public function __construct($id, $language, $name)
    {
        $this->id = (int) $id;
        $this->language = (int) $language;
        $this->name = (string) $name;
    }

    /**
     * Get topic id
     *
     * @return int
     */
    public function getTopicId()
    {
        return $this->id;
    }

    /**
     * Get language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->language;
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

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * Get view
     *
     * @return Newscoop\View\TopicView
     */
    public function getView()
    {
        $view = new TopicView();
        $view->identifier = $this->id;
        $view->defined = true;
        $view->name = $this->name;
        $view->value = sprintf('%s:%s', $this->name, $this->language);
        return $view;
    }
}
