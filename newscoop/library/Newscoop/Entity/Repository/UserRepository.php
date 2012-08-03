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
use Newscoop\Entity\Article;

/**
 * User repository
 */
class UserRepository extends EntityRepository implements \Newscoop\Search\RepositoryInterface
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
        if (!$user->getId()) {
            // must persist user before adding attributes
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
        $qb->andWhere($qb->expr()->notIn('u.id', $this->getEditorIds($editorRoles)));

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
            $user->setPoints($result['comments'] + $result['articles']);
            $users[] = $user;
        }

        return $users;
    }

    public function findVerifiedUsers($countOnly, $offset, $limit)
    {
        if ($countOnly) {
            $qb = $this->getEntityManager()->createQuery('SELECT COUNT(u.id) FROM Newscoop\Entity\User u JOIN u.attributes a WHERE a.attribute = \'is_verified\' AND a.value = 1');
            return $qb->getSingleScalarResult();
        }

        $qb = $this->getEntityManager()->createQuery('SELECT u FROM Newscoop\Entity\User u JOIN u.attributes a WHERE a.attribute = \'is_verified\' AND a.value = 1');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        return $qb->getResult();
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
     * Return Users if any of their searched attributes contain the searched term.
     *
     * @param string $search
     * @param bool $countOnly
     * @param int $offset
     * @param int $limit
     * @return array Newscoop\Entity\User
     */
    public function searchUsers($search, $countOnly, $offset, $limit)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u')
            ->from('Newscoop\Entity\User', 'u');

        $qb->where("u.username LIKE :q")
            ->andWhere('u.status = :status')
            ->andWhere('u.is_public = :public');

        $qb->setParameters(array(
            'q' => '%' . trim($search, '%') . '%',
            'status' => User::STATUS_ACTIVE,
            'public' => TRUE,
        ));

        if ($countOnly) {
            return $qb->select('COUNT(u)')
                ->getQuery()
                ->getSingleScalarResult();
        }

        $qb->orderBy('u.username', 'ASC');
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
     * @param array $editorRoles
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findEditors(array $editorRoles, $limit = 25, $offset = 0)
    {
        $editorIds = $this->getEditorIds($editorRoles);
        if (empty($editorIds)) {
            return array();
        }

        $expr = $this->getEntityManager()->getExpressionBuilder();
        $query = $this->createPublicUserQueryBuilder()
            ->andWhere($expr->in('u.id', $this->getEditorIds($editorRoles)))
            ->orderBy('u.username', 'asc')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Get editors count
     *
     * @param array $editorRoles
     * @return int
     */
    public function getEditorsCount(array $editorRoles)
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();
        $query = $this->createPublicUserQueryBuilder()
            ->select('COUNT(u)')
            ->andWhere($expr->in('u.id', $this->getEditorIds($editorRoles)))
            ->getQuery();

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
     * Find by first character of username
     *
     * @param array $characters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findByUsernameFirstCharacterIn(array $characters, $limit = 25, $offset = 0)
    {
        if (empty($characters)) {
            throw new \InvalidArgumentException("Characters can't be empty");
        }

        $query = $this->createQueryBuilder('u')
            ->where($this->getUsernameFirstCharacterWhere($characters))
            ->orderBy('u.username', 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * Count by first character of username
     *
     * @param array $characters
     * @return int
     */
    public function countByUsernameFirstCharacterIn(array $characters)
    {
        if (empty($characters)) {
            throw new \InvalidArgumentException("Characters can't be empty");
        }

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(u)')
            ->from($this->getEntityName(), 'u')
            ->where($this->getUsernameFirstCharacterWhere($characters))
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    /**
     * Get posts count for user
     *
     * $param Newscoop\Entity\User $user
     * @return int
     */
    public function getUserPostsCount(User $user)
    {
        $author = $user->getAuthor();
        if ($author === null) {
            return 0;
        }

        $expr = $this->getEntityManager()->getExpressionBuilder();
        $query = $this->getEntityManager()->getRepository('Newscoop\Entity\Article')
            ->createQueryBuilder('article')
            ->select('COUNT(article)')
            ->innerJoin('article.authors', 'author', 'WITH', 'author = :author')
            ->where('article.workflowStatus = :status')
            ->andWhere($expr->in('article.type', array('news', 'blog')))

            ->getQuery();

        $query->setParameters(array(
            'status' => Article::STATUS_PUBLISHED,
            'author' => $author,
        ));

        return $query->getSingleScalarResult();
    }

    /**
     * Get first characters constraint
     *
     * @param array $characters
     * @return Doctrine\ORM\Query\Expr\Orx
     */
    private function getUsernameFirstCharacterWhere(array $characters)
    {
        $expr = $this->getEntityManager()->getExpressionBuilder();
        $characterWhere = $expr->orx();
        foreach ($characters as $i => $character) {
            $characterWhere->add($expr->like('LOWER(u.username)', $expr->literal($character . '%')));
        }

        $where = $expr->andX();
        $where->add($expr->eq('u.status', User::STATUS_ACTIVE));
        $where->add($expr->eq('u.is_public', true));
        $where->add($characterWhere);
        return $where;
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
     * Find users for indexing
     *
     * @return array
     */
    public function getBatch()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.indexed IS NULL OR u.indexed < u.updated')
            ->getQuery()
            ->setMaxResults(50)
            ->getResult();
    }

    /**
     * Set indexed now
     *
     * @param array $users
     * @return void
     */
    public function setIndexedNow(array $users)
    {
        if (empty($users)) {
            return;
        }

        $this->getEntityManager()->createQuery('UPDATE Newscoop\Entity\User u SET u.indexed = CURRENT_TIMESTAMP() WHERE u.id IN (:users)')
            ->setParameter('users', array_map(function($user) { return $user->getId(); }, $users))
            ->execute();
    }

    /**
     * Set indexed null
     *
     * @return void
     */
    public function setIndexedNull()
    {
        $this->getEntityManager()->createQuery('UPDATE Newscoop\Entity\User u SET u.indexed = NULL')
            ->execute();
    }

    /**
     * Get newscoop login count
     *
     * @return int
     */
    public function getNewscoopLoginCount()
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->leftJoin('u.identities', 'ui')
            ->where('ui.user IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get external login count
     *
     * @return int
     */
    public function getExternalLoginCount()
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u)')
            ->leftJoin('u.identities', 'ui')
            ->where('ui.user IS NOT NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get user points
     *
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function getUserPoints(User $user)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u.id, ' . $this->getUserPointsSelect())
            ->where('u.id = :user')
            ->getQuery();

        $query->setParameter('user', $user->getId());
        $result = $query->getSingleResult();
        $user->setPoints($result['comments'] + $result['articles']);
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

        $articlesCount = "(SELECT COUNT(a)";
        $articlesCount .= " FROM Newscoop\Entity\Article a, Newscoop\Entity\ArticleAuthor aa";
        $articlesCount .= " WHERE ";
        $articlesCount .= implode(' AND ', array(
            'a.number = aa.articleNumber',
            'a.language = aa.languageId',
            'aa.authorId = u.author',
            "a.type IN ('news', 'blog')",
            sprintf("a.workflowStatus = '%s'", Article::STATUS_PUBLISHED),
        ));
        $articlesCount .= ") as articles";

        return "{$commentsCount}, {$articlesCount}";
    }
}
