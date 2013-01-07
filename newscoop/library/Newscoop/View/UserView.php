<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\View;

/**
 * User View
 */
class UserView extends View
{
    /**
     * @var int
     */
    public $identifier;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $email;

    /**
     * @var array
     */
    public $attributes = array();
}
