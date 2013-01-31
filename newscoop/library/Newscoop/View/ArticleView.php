<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\View;

/**
 * Article View
 */
class ArticleView extends View
{
    /**
     * @var int
     */
    public $number;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $webcode;

    /**
     * @var string
     */
    public $language;

    /**
     * @var int
     */
    public $publication_number;

    /**
     * @var int
     */
    public $issue_number;

    /**
     * @var int
     */
    public $section_number;

    /**
     * @var array
     */
    public $authors = array();

    /**
     * @var array
     */
    public $keywords = array();

    /**
     * @var array
     */
    public $topics = array();

    /**
     * @var array
     */
    public $fields = array();

    /**
     * @var DateTime
     */
    public $created;

    /**
     * @var DateTime
     */
    public $updated;

    /**
     * @var DateTime
     */
    public $published;

    /**
     * @var DateTime
     */
    public $indexed;
}
