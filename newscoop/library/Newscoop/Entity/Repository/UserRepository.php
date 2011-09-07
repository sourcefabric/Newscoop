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
    private $setters = array(
        'username' => 'setUsername',
        'password' => 'setPassword',
        'first_name' => 'setFirstName',
        'last_name' => 'setLastName',
        'email' => 'setEmail',
        'status' => 'setStatus',
        'is_admin' => 'setAdmin',
        'is_public' => 'setPublic',
        'image' => 'setImage',
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
        $this->setProperties($user, $values);

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

        $this->setAttributes($user, array_key_exists('attributes', $values) ? $values['attributes'] : array());
        $this->setUserTypes($user, array_key_exists('user_type', $values) ? $values['user_type'] : array());

        $this->getEntityManager()->persist($user);
    }

    /**
     * Get total count for given criteria
     *
     * @param array $criteria
     * @return int
     */
    public function countBy(array $criteria)
    {
        return count($this->findBy($criteria));
    }

    /**
     * Set user properties
     *
     * @param Newscoop\Entity\User $user
     * @param array $values
     * @return void
     */
    private function setProperties(User $user, array $values)
    {
        foreach ($this->setters as $property => $setter) {
            if (array_key_exists($property, $values)) {
                $user->$setter($values[$property]);
            }
        }
    }

    /**
     * Set user attributes
     *
     * @param Newscoop\Entity\User $user
     * @param array $attributes
     * @return void
     */
    private function setAttributes(User $user, array $attributes)
    {
        if (!$user->getId()) { // must persist user before adding attributes
            $this->getEntityManager()->persist($user);
            $this->getEntityManager()->flush();
        }

        foreach ($attributes as $name => $value) {
            $user->addAttribute($name, $value);
        }
    }

    /**
     * Set user types
     *
     * @param Newscoop\Entity\User $user
     * @param array $types
     * @return void
     */
    private function setUserTypes(User $user, array $types)
    {
        $user->getUserTypes()->clear();
        foreach ($types as $type) {
            $user->addUserType($this->getEntityManager()->getReference('Newscoop\Entity\User\Group', $type));
        }
    }

    /**
     * Test if property value is unique
     *
     * @param string $property
     * @param string $value
     * @param int $id
     * @return bool
     */
    public function isUnique($property, $value, $id = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u.id)')
            ->from('Newscoop\Entity\User', 'u')
            ->where("u.{$property} = ?0");

        $params = array($value);

        if ($id !== null) {
            $qb->andWhere('u.id <> ?1');
            $params[] = $id;
        }

        $qb->setParameters($params);

        return !$qb->getQuery()->getSingleScalarResult();
    }

    public function findActiveUsers($countOnly, $offset, $limit)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        if ($countOnly) {
            $qb->select('COUNT(u.id)');
        }
        else {
            $qb->select('u');
        }

        $qb->from('Newscoop\Entity\User', 'u');

        $qb->where($qb->expr()->eq("u.status", User::STATUS_ACTIVE));
        $qb->andWhere($qb->expr()->eq("u.is_public", true));

        if ($countOnly === false) {
            $qb->orderBy('u.points', 'DESC');
            $qb->addOrderBy('u.id', 'ASC');

            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);

            echo $qb->getQuery()->getSql();

            return $qb->getQuery()->getResult();
        }
        else {
            return $qb->getQuery()->getOneOrNullResult();
        }
    }

    /**
     * Return Users if their last name begins with one of the letter passed in.
     *
     * @param array $letters = ['a', 'b']
     *
     * @return array Newscoop\Entity\User
     */
    public function findUsersLastNameInRange($letters, $countOnly, $offset, $limit)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        if ($countOnly) {
            $qb->select('COUNT(u.id)');
        }
        else {
            $qb->select('u');
        }

        $qb->from('Newscoop\Entity\User', 'u');

        $qb->where($qb->expr()->like("u.last_name", "'$letters[0]%'"));
        for ($i=1; $i < count($letters); $i++) {
            $qb->orWhere($qb->expr()->like("u.last_name", "'$letters[$i]%'"));
        }

        if ($countOnly === false) {
            $qb->orderBy('u.last_name', 'ASC');
            $qb->addOrderBy('u.first_name', 'ASC');
            $qb->addOrderBy('u.id', 'ASC');

            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);

            //echo $qb->getQuery()->getSql();

            return $qb->getQuery()->getResult();
        }
        else {
            return $qb->getQuery()->getOneOrNullResult();
        }
    }

    /**
     * Return Users if any of their searched attributes contain the searched term.
     *
     * @param string $search
     *
     * @param array $attributes
     *
     * @return array Newscoop\Entity\User
     */
    public function searchUsers($search, $countOnly, $offset, $limit, $attributes = array("first_name", "last_name", "username"))
    {
        $keywords = explode(" ", $search);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')
            ->from('Newscoop\Entity\User', 'u');

        $outerAnd = $qb->expr()->andx();

        for($i=0; $i < count($keywords); $i++) {
            $innerOr = $qb->expr()->orx();
            for ($j=0; $j < count($attributes); $j++) {
                $innerOr->add($qb->expr()->like("u.{$attributes[$j]}", "'%$keywords[$i]%'"));
            }
            $outerAnd->add($innerOr);
        }

        $qb->where($outerAnd);
        $qb->orderBy('u.last_name', 'ASC');
        $qb->addOrderBy('u.first_name', 'ASC');
        $qb->addOrderBy('u.id', 'ASC');

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        //echo $qb->getQuery()->getSql();

        return $qb->getQuery()->getResult();
    }
}
