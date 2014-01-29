<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\User;

use Newscoop\Criteria;
use Newscoop\Entity\User;

/**
 * Available criteria for users listing.
 */
class UserCriteria extends Criteria
{
    /**
     * @var array
     */
    public $attributes;

    /**
     * @var string
     */
    public $status = User::STATUS_ACTIVE;

    /**
     * @var bool
     */
    public $is_public = true;

    /**
     * @var string
     */
    public $created;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @var boolean
     */
    public $is_admin = false;

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

    /**
     * @var string
     */
    public $query;
}