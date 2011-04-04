<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\User,
    Newscoop\Entity\Acl\Role;

/**
 * User repository
 */
class UserRepository extends EntityRepository
{
    /**
     * Save entity
     *
     * @param Newscoop\Entity\User $user
     * @param array $values
     * @return void
     */
    public function save(User $user, array $values)
    {
        $em = $this->getEntityManager();

        $user->setName($values['name'])
            ->setEmail($values['email'])
            ->setPhone($values['phone'])
            ->setTitle($values['title'])
            ->setGender($values['gender'])
            ->setAge($values['age'])
            ->setCity($values['city'])
            ->setStreetAddress($values['street_address'])
            ->setPostalCode($values['postal_code'])
            ->setState($values['state'])
            ->setCountry($values['country'])
            ->setFax($values['fax'])
            ->setContactPerson($values['contact_person'])
            ->setPhoneSecond($values['phone_second'])
            ->setEmployer($values['employer'])
            ->setEmployerType($values['employer_type'])
            ->setPosition($values['position']);

        // set groups
        $groups = $user->getGroups();
        $groups->clear();
        foreach ($values['roles'] as $roleId) {
            $group = $em->getReference('Newscoop\Entity\User\Group', (int) $roleId);
            $groups->add($group);
        }

        // set username/password
        if ($user->getId() > 0) { // edit
        } else { // add
            $role = new Role();
            $em->persist($role);

            $user->setUsername($values['username'])
                ->setPassword($values['password'])
                ->setRole($role);
        }

        $em->persist($user);
    }
}
