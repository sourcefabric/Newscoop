<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use Newscoop\Entity\User;
use Newscoop\User\UserCriteria;
use Newscoop\ListResult;

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

        if (array_key_exists('attributes', $values)) {
            $this->setAttributes($user, (array) $values['attributes']);
        }

        if (array_key_exists('user_type', $values)) {
            $this->setUserTypes($user, (array) $values['user_type']);
        }

        if (array_key_exists('author', $values)) {
            $author = null;
            if (!empty($values['author'])) {
                $author = $this->getEntityManager()->getReference('Newscoop\Entity\Author', $values['author']);
            }
            $user->setAuthor($author);
        }

        $this->getEntityManager()->persist($user);
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
            ->where("LOWER(u.{$property}) = LOWER(?0)");

        $params = array($value);

        if ($id !== null) {
            $qb->andWhere('u.id <> ?1');
            $params[] = $id;
        }

        $qb->setParameters($params);

        return !$qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Find active members of community
     *
     * @param bool $countOnly
     * @param int $offset
     * @param int $limit
     * @param array $editorRoles
     * @return array|int
     */
    public function findActiveUsers($countOnly, $offset, $limit, array $editorRoles)
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();
        $qb = $this->createPublicUserQueryBuilder();

        $editorIds = $this->getEditorIds($editorRoles);
        if (!empty($editorIds)) {
            $qb->andWhere($qb->expr()->in('u.id', $editorIds));
        }

        if ($countOnly) {
            $qb->select('COUNT(u.id)');

            return $qb->getQuery()->getSingleScalarResult();
        }

        $qb->select('u, ' . $this->getUserPointsSelect());
        $qb->orderBy('comments', 'DESC');
        $qb->addOrderBy('u.id', 'ASC');
        $qb->groupBy('u.id');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        $users = array();
        $results = $qb->getQuery()->getResult();

        foreach ($results as $result) {
            $user = $result[0];
            $user->setPoints((int) $result['comments']);
            $users[] = $user;
        }
        
        return $users;
    }

    /**
     * Create query builder for public users
     *
     * @return Doctrine\ORM\QueryBuilder
     */
    private function createPublicUserQueryBuilder()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.status = :status')
            ->andWhere('u.is_public = :public')
            ->setParameter('status', User::STATUS_ACTIVE)
            ->setParameter('public', true);
    }

    /**
     * Get editor ids
     *
     * @param array $editorRoles
     * @return array
     */
    private function getEditorIds(array $editorRoles)
    {
        if (empty($editorRoles)) {
            return array();
        }

        $expr = $this->getEntityManager()->getExpressionBuilder();
        $query = $this->createQueryBuilder('u')
            ->select('DISTINCT(u.id)')
            ->innerJoin('u.groups', 'g', Expr\Join::WITH, $expr->in('g.id', $editorRoles))
            ->getQuery();

        $ids = array_map(function($row) {
            return (int) $row['id'];
        }, $query->getResult());
        return $ids;
    }

    /**
     * Get user points select statement
     *
     * @return string
     */
    private function getUserPointsSelect()
    {
        $commentsCount = "(SELECT COUNT(c)";
        $commentsCount .= " FROM Newscoop\Entity\Comment c, Newscoop\Entity\Comment\Commenter cc";
        $commentsCount .= " WHERE c.commenter = cc AND cc.user = u) as comments";

        return "{$commentsCount}";
    }

    /**
     * Return Users if their last name begins with one of the letter passed in.
     *
     * @param array $letters = ['a', 'b']
     *
     * @return array Newscoop\Entity\User
     */
    public function findUsersLastNameInRange($letters, $countOnly, $offset, $limit, $firstName = false)
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

        $letterIndex = $qb->expr()->orx();
        for ($i=0; $i < count($letters); $i++) {
            $letterIndex->add($qb->expr()->like("LOWER(u.last_name)", "'$letters[$i]%'"));
            if ($firstName) {
                $letterIndex->add($qb->expr()->like("LOWER(u.first_name)", "'$letters[$i]%'"));
            }
        }
        $qb->andWhere($letterIndex);

        if ($countOnly === false) {
            $qb->orderBy('u.username', 'ASC');
            $qb->addOrderBy('u.id', 'ASC');

            $qb->setFirstResult($offset);
            $qb->setMaxResults($limit);

            return $qb->getQuery()->getResult();
        } else {
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
                $innerOr->add($qb->expr()->like("u.{$attributes[$j]}", "'$keywords[$i]'"));
            }
            $outerAnd->add($innerOr);
        }

        $outerAnd->add($qb->expr()->eq("u.status", User::STATUS_ACTIVE));
        $outerAnd->add($qb->expr()->eq("u.is_public", true));

        $qb->where($outerAnd);

        $qb->orderBy('u.last_name', 'ASC');
        $qb->addOrderBy('u.first_name', 'ASC');
        $qb->addOrderBy('u.id', 'DESC');

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get random list of users
     *
     * @param int $limit
     * @return array
     */
    public function getRandomList($limit)
    {
        $query = $this->getEntityManager()->createQuery("SELECT u, RAND() as random FROM {$this->getEntityName()} u WHERE u.status = :status AND u.is_public = :public ORDER BY random");
        $query->setMaxResults($limit);
        $query->setParameters(array(
            'status' => User::STATUS_ACTIVE,
            'public' => True,
        ));

        $users = array();
        foreach ($query->getResult() as $result) {
            $users[] = $result[0];
        }

        return $users;
    }

    /**
     * Get editors
     *
     * @param int $blogRole
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findEditors($blogRole, $limit, $offset)
    {
        $query = $this->createQueryBuilder('u')
            ->leftJoin('u.groups', 'g', Expr\Join::WITH, 'g.id = ' . $blogRole)
            ->where('u.is_admin = :admin')
            ->andWhere('u.status = :status')
            ->andWhere('u.author IS NOT NULL')
            ->andWhere('g.id IS NULL')
            ->orderBy('u.username', 'asc')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        $query->setParameters(array(
            'admin' => 1,
            'status' => User::STATUS_ACTIVE,
        ));

        return $query->getResult();
    }

    /**
     * Get editors count
     *
     * @param int $blogRole
     * @return int
     */
    public function getEditorsCount($blogRole)
    {
        $query = $this->getEntityManager()
            ->createQueryBuilder()
            ->select('COUNT(u)')
            ->from($this->getEntityName(), 'u')
            ->leftJoin('u.groups', 'g', Expr\Join::WITH, 'g.id = ' . $blogRole)
            ->where('u.is_admin = :admin')
            ->andWhere('u.status = :status')
            ->andWhere('u.author IS NOT NULL')
            ->andWhere('g.id IS NULL')
            ->getQuery();

        $query->setParameters(array(
            'admin' => 1,
            'status' => User::STATUS_ACTIVE,
        ));

        return $query->getSingleScalarResult();
    }

    /**
     * Get total users count
     *
     * @return int
     */
    public function countAll()
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u)')
            ->from($this->getEntityName(), 'u')
            ->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    /**
     * Get users count for given criteria
     *
     * @param array $criteria
     * @return int
     */
    public function countBy(array $criteria)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u)')
            ->from($this->getEntityName(), 'u');

        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $queryBuilder->andWhere("u.$property = :$property");
            }
        }

        $query = $queryBuilder->getQuery();
        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $query->setParameter($property, $value);
            }
        }

        return (int) $query->getSingleScalarResult();
    }

    /**
     * Delete user
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function delete(User $user)
    {
        if ($user->isPending()) {
            $this->getEntityManager()->remove($user);
        } else {
            $user->setStatus(User::STATUS_DELETED);
            $user->setEmail(null);
            $user->setFirstName(null);
            $user->setLastName(null);
            $this->removeAttributes($user);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * Remove user attributes
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    private function removeAttributes(User $user)
    {
        $attributes = $this->getEntityManager()->getRepository('Newscoop\Entity\UserAttribute')->findBy(array(
            'user' => $user->getId(),
        ));

        foreach ($attributes as $attribute) {
            $user->addAttribute($attribute->getName(), null);
            $this->getEntityManager()->remove($attribute);
        }
    }

    /**
     * Get list for given criteria
     *
     * @param Newscoop\User\UserCriteria $criteria
     * @return Newscoop\ListResult
     */
    public function getListByCriteria(UserCriteria $criteria)
    {
        $qb = $this->createQueryBuilder('u');

        $qb->andWhere('u.status = :status')
            ->setParameter('status', $criteria->status);

        $qb->andWhere('u.is_public = :is_public')
            ->setParameter('is_public', $criteria->is_public);

        if (!empty($criteria->groups)) {
            $qb->leftJoin('u.groups', 'g', Expr\Join::WITH, 'g.id IN (:groups)');
            $qb->setParameter('groups', $criteria->groups);
            $qb->andWhere($criteria->excludeGroups ? 'g.id IS NULL' : 'g.id IS NOT NULL');
        }

        if (!empty($criteria->query)) {
            $qb->andWhere("(u.username LIKE :query)");
            $qb->setParameter('query', '%' . trim($criteria->query, '%') . '%');
        }

        if (!empty($criteria->nameRange)) {
            $this->addNameRangeWhere($qb, $criteria->nameRange);
        }

        $list = new ListResult();
        $list->count = (int) $qb->select('COUNT(u)')->getQuery()->getSingleScalarResult();

        $qb->select('u, ' . $this->getUserPointsSelect());
        $qb->setFirstResult($criteria->firstResult);
        $qb->setMaxResults($criteria->maxResults);

        $metadata = $this->getClassMetadata();
        foreach ($criteria->orderBy as $key => $order) {
            if (array_key_exists($key, $metadata->columnNames)) {
                $key = 'u.' . $key;
            }

            $qb->orderBy($key, $order);
        }

        $list->items = array_map(function ($row) {
            $user = $row[0];
            $user->setPoints((int) $row['comments']);
            return $user;
        }, $qb->getQuery()->getResult());

        return $list;
    }

    /**
     * Add name first letter where condition to query builder
     *
     * @param Doctrine\ORM\QueryBuilder $qb
     * @param array $letters
     * @return void
     */
    private function addNameRangeWhere($qb, array $letters)
    {
        $orx = $qb->expr()->orx();
        foreach ($letters as $letter) {
            $orx->add($qb->expr()->like(
                'u.username',
                $qb->expr()->literal(substr($letter, 0, 1) . '%')
            ));
        }

        $qb->andWhere($orx);
    }
}
