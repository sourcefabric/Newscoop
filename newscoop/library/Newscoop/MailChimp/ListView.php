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
class ListView extends ValueObject
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var array
     */
    public $groups = array();
}
