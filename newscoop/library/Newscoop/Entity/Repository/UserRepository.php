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

        if (!$user->getUsername()) {
            throw new \InvalidArgumentException('username_empty');
        }

        if (!$this->isUnique('username', $user->getUsername(), $user->getId())) {
            throw new \InvalidArgumentException('username_conflict');
        }

        if (!$user->getEmail()) {
            throw new \InvalidArgumentException('email_empty');
        }

        if (!$this->isUnique('email', $user->getEmail(), $user->getId())) {
            throw new \InvalidArgumentException('email_conflict');
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

        $this->getEntityManager()->persist($user);
    }

    /**
     * Test if property value is unique
     *
     * @param string $property
     * @param string $value
     * @param int $id
     * @return bool
     */
    protected function isUnique($property, $value, $id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from('Newscoop\Entity\User', 'u')
            ->where("u.{$property} = :value");

        $params = array(
            'value' => $value,
        );

        if ($id > 0) {
            $qb->andWhere('u.id <> :id');
            $params['id'] = $id;
        }

        $qb->setParameters($params);

        return !$qb->getQuery()->getSingleScalarResult();
    }
}
