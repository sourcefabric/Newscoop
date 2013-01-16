<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Topic;

use Newscoop\Command;

/**
 */
class SaveUserTopicsCommand extends Command
{
    /**
     * @var array
     */
    public $topics = array();

    /**
     * @var array
     */
    public $selected = array();

    /**
     * @var int
     */
    public $userId;

    /**
     * @var int
     */
    public $languageId;
}
