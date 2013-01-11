<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\User;

use Newscoop\Entity\User;

/**
 */
class UserCriteria
{
    /**
     * @var string
     */
    public $status = User::STATUS_ACTIVE;

    /**
     * @var bool
     */
    public $is_public = true;

    /**
     * @var int
     */
    public $firstResult = 0;

    /**
     * @var int
     */
    public $maxResults = 25;

    /**
     * @var array
     */
    public $orderBy = array('username' => 'asc');

    /**
     * @var array
     */
    public $groups = array();

    /**
     * @var bool
     */
    public $excludeGroups = false;

    /**
     * @var array
     */
    public $nameRange = array();
}
