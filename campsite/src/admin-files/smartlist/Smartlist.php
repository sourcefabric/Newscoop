<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */
class Smartlist
{
    /** @var string */
    private $web = '';

    /** @var string */
    private $admin = '';

    /** @var int */
    private $publication = NULL;

    /** @var int */
    private $issue = NULL;

    /** @var int */
    private $section = NULL;

    /** @var int */
    private $language = NULL;

    /**
     * @param string $web
     * @param string $admin
     */
    public function __construct($web, $admin)
    {
        // set paths
        $this->web = $web;
        $this->admin = $admin;

        camp_load_translation_strings('articles');
        camp_load_translation_strings('universal_list');
    }

    /**
     * Set publication.
     * @param int $publication
     * @return Smartlist
     */
    public function setPublication($publication)
    {
        $this->publication = empty($publication) ? NULL : (int) $publication;
        return $this;
    }

    /**
     * Set issue.
     * @param int $issue
     * @return Smartlist
     */
    public function setIssue($issue)
    {
        $this->issue = empty($issue) ? NULL : (int) $issue;
        return $this;
    }

    /**
     * Set section.
     * @param int $section
     * @return Smartlist
     */
    public function setSection($section)
    {
        $this->section = empty($section) ? NULL : (int) $section;
        return $this;
    }

    /**
     * Set language.
     * @param int $language
     * @return Smartlist
     */
    public function setLanguage($language)
    {
        $this->language = empty($language) ? NULL : (int) $language;
        return $this;
    }

    /**
     * Render filters.
     * @return Smartlist
     */
    public function renderFilters()
    {
        include dirname(__FILE__) . '/filters.php';
        return $this;
    }

    /**
     * Render actions.
     * @return Smartlist
     */
    public function renderActions()
    {
        include dirname(__FILE__) . '/actions.php';
        return $this;
    }

    /**
     * Render table.
     * @return Smartlist
     */
    public function renderTable()
    {
        include dirname(__FILE__) . '/table.php';
        return $this;
    }
}
