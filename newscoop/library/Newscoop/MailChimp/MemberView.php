<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\MailChimp;

use Newscoop\ValueObject;

/**
 */
class MemberView extends ValueObject
{
    /**
     * @var string
     */
    public $email;

    /**
     * @var bool
     */
    public $subscriber = false;

    /**
     * @var array
     */
    public $groups = array();
}
