<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\User;

/**
 * Base user repository
 */
abstract class UserRepository extends EntityRepository
{
    /** @var array */
    private static $defaults = array(
        'phone' => '',
        'title' => '',
        'gender' => '',
        'age' => '',
        'city' => '',
        'street_address' => '',
        'postal_code' => '',
        'state' => '',
        'country' => '',
        'fax' => '',
        'contact_person' => '',
        'phone_second' => '',
        'employer' => '',
        'employer_type' => '',
        'position' => '',
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
        $em = $this->getEntityManager();
        $values += self::$defaults;

        // check for unique email
        $query = $em->createQuery('SELECT u.id FROM Newscoop\Entity\User u WHERE u.email = ?1')
            ->setParameter(1, $values['email']);
        $conflicts = $query->getResult();
        foreach ($conflicts as $conflict) {
            if ($conflict['id'] != $user->getId()) {
                throw new \InvalidArgumentException('email');
            }
        }

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

        if (!empty($values['username'])) {
            $user->setUsername($values['username']);
        }

        // set username/password
        if ($user->getId() > 0) { // update
            if (!empty($values['password'])) {
                $user->setPassword($values['password']);
            }
        } else { // insert
            $user->setPassword($values['password']);
        }

        $em->persist($user);
    }

    /**
     * Delete user
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function delete(User $user)
    {
        $em = $this->getEntityManager();
        $em->remove($user);
    }
}
