<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\User;

/**
 * User repository
 */
class UserRepository extends EntityRepository
{
    /** @var array */
    private static $mapping = array(
        'username' => 'setUsername',
        'password' => 'setPassword',
        'first_name' => 'setFirstName',
        'last_name' => 'setLastName',
        'email' => 'setEmail',
        'status' => 'setStatus',
    );

    /**
     * Save user
     *
     * @param Newscoop\Entity\User $user
     * @param array $values
     * @return void
     */
    public function save(User $user, array $values)
    {
        // set common properties
        foreach (self::$mapping as $property => $setter) {
            if (array_key_exists($property, $values)) {
                $user->$setter($values[$property]);
            }
        }

        // set attributes
        if (!empty($values['attributes'])) {
            if (!$user->getId()) { // must persist user before adding attributes
                $this->getEntityManager()->persist($user);
                $this->getEntityManager()->flush();
            }

            foreach ($values['attributes'] as $key => $value) {
                $user->addAttribute($key, $value);
            }
        }

        if (!$user->getUsername()) {
            throw new \InvalidArgumentException("Username can't be empty");
        }

        $this->getEntityManager()->persist($user);
    }
}
