<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\View;

/**
 * Topic View
 */
class TopicView extends View
{
    /**
     * @var int
     */
    public $identifier;

    /**
     * @var bool
     */
    public $defined = false;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;
}
