<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\User;

use Newscoop\Entity\User;

/**
 * Subscriber entity
 * @entity(repositoryClass="Newscoop\Entity\Repository\User\SubscriberRepository")
 */
class Subscriber extends User
{
    /**
     */
    public function __construct()
    {
        parent::__construct();
        $this->reader = 'Y';
    }
}
