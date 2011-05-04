<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository\User;

use Newscoop\Entity\User\Subscriber,
    Newscoop\Entity\Repository\UserRepository;

/**
 * Subscriber repository
 */
class SubscriberRepository extends UserRepository
{
    public function save(Subscriber $subscriber, array $values)
    {
        $map = array(
            'Name' => 'name',
            'EMail' => 'email',
            'handle' => 'username',
            'passwd' => 'password',
            'City' => 'city',
            'Phone' => 'phone',
            'Phone2' => 'phone_second',
        );

        foreach ($map as $old => $new) {
            if (isset($values[$old])) {
                $values[$new] = $values[$old];
            }
        }

        parent::save($subscriber, $values);
    }
}
