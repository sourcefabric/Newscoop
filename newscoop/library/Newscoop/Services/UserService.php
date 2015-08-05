<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Newscoop\Entity\User;
use Newscoop\PaginatedCollection;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\GenericEvent;
use Newscoop\Exception\AuthenticationException;

/**
 * User service
 */
class UserService
{
    const USER_ENTITY = 'Newscoop\Entity\User';

    /** @var \Doctrine\Common\Persistence\ObjectManager */
    protected $em;

    /** @var \Zend_Auth */
    protected $auth;

    /** @var \Newscoop\Entity\User */
    protected $currentUser;

    /** @var \Newscoop\Entity\Repository\UserRepository */
    protected $repository;

    /** @var SecurityContext */
    protected $security;

    /** @var $userIp */
    protected $userIp;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Zend_Auth                  $auth
     * @param SecurityContext            $security
     */
    public function __construct(ObjectManager $em, \Zend_Auth $auth, SecurityContext $security)
    {
        $this->em = $em;
        $this->auth = $auth;
        $this->security = $security;
    }

    /**
     * Get current user
     *
     * @return Newscoop\Entity\User
     */
    public function getCurrentUser()
    {
        if ($this->currentUser === null) {
            if ($this->auth->hasIdentity()) {
                $this->currentUser = $this->getRepository()->find($this->auth->getIdentity());
            } elseif ($this->security->getToken()) {
                if ($this->security->getToken()->getUser()) {
                    $currentUser = $this->security->getToken()->getUser();
                    if ($this->security->isGranted('IS_AUTHENTICATED_FULLY') ||
                        $this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')
                    ) {
                        $this->currentUser = $currentUser;
                    } else {
                        throw new AuthenticationException();
                    }
                } else {
                    throw new AuthenticationException();
                }
            }
        }

        return $this->currentUser;
    }

    /**
     * Find user
     *
     * @param int $id
     *
     * @return Newscoop\Entity\User
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * Find all users
     *
     * @return mixed
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * Find by given criteria
     *
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return mixed
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get collection by given criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int   $limit
     * @param int   $offset
     *
     * @return Newscoop\PaginatedCollection
     */
    public function getCollection(array $criteria, array $orderBy, $limit = null, $offset = null)
    {
        $qb = $this->getRepository()->createQueryBuilder('u');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        if (!empty($criteria['q'])) {
            $q = $qb->expr()->literal('%'.$criteria['q'].'%');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->like('u.username', $q),
                $qb->expr()->like('u.email', $q)
            ));
        }

        if (!empty($criteria['groups'])) {
            $qb->join('u.groups', 'g', 'WITH', 'g.id = :group')
                ->setParameter('group', $criteria['groups']);
        }

        if (isset($criteria['status'])) {
            $qb->andWhere('u.status = :status')
                ->setParameter('status', $criteria['status']);
        }

        if (isset($criteria['attribute']) && is_array($criteria['attribute'])) {
            foreach ($criteria['attribute'] as $attribute => $value) {
                $qb->join('u.attributes', 'a', 'WITH', 'a.attribute = :attribute AND a.value = :value');
                $qb->setParameter('attribute', $attribute);
                $qb->setParameter('value', $value);
                break; // only 1
            }
        }

        foreach ($orderBy as $column => $dir) {
            $qb->addOrderBy("u.$column", $dir);
        }

        return new PaginatedCollection($qb->getQuery());
    }

    /**
     * Find one by given criteria
     *
     * @param array $criteria
     *
     * @return Newscoop\Entity\User
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Save user
     *
     * @param array                $data
     * @param Newscoop\Entity\User $user
     *
     * @return Newscoop\Entity\User
     */
    public function save(array $data, User $user = null)
    {
        if (null === $user) {
            $user = new User();
        }

        if (empty($data['image'])) {
            unset($data['image']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $this->getRepository()->save($user, $data);
        $this->em->flush();

        return $user;
    }

    /**
     * Delete user
     *
     * @param Newscoop\Entity\User $user
     *
     * @return void
     */
    public function delete(User $user)
    {
        if ($this->auth->getIdentity() == $user->getId()) {
            throw new \InvalidArgumentException("You can't delete yourself");
        }

        $this->getRepository()->delete($user);
    }

    /**
     * Rename user
     *
     * @param object $command
     *
     * @return void
     */
    public function renameUser($command)
    {
        $user = $this->getRepository()->findOneById($command->userId);
        if ($user->render()->username === $command->username) {
            return;
        }

        $conflict = $this->getRepository()->findByUsername($command->username);
        if (!empty($conflict)) {
            throw new InvalidArgumentException($command->username);
        }

        $user->rename($command->username);
        $this->em->flush();
    }

    /**
     * Generate username
     *
     * @param string $firstName
     * @param string $lastName
     *
     * @return string
     */
    public function generateUsername($firstName, $lastName)
    {
        if (empty($firstName) && empty($lastName)) {
            return '';
        }

        $user = new User();
        $user->setUsername(trim($firstName).' '.trim($lastName));
        $username = $user->getUsername();

        for ($i = '';; $i++) {
            $conflict = $this->getRepository()->findOneBy(array(
                'username' => "$username{$i}",
            ));

            if (empty($conflict)) {
                return "$username{$i}";
            }
        }
    }

    /**
     * Set user active
     *
     * @param Newscoop\Entity\User $user
     *
     * @return void
     */
    public function setActive(User $user)
    {
        $user->setStatus(User::STATUS_ACTIVE);
        $this->em->flush();
    }

    /**
     * Create pending user
     *
     * @param string      $email
     * @param string|null $firstName
     * @param string|null $lastName
     * @param string|null $subscriber
     *
     * @return Newscoop\Entity\User
     */
    public function createPending($email, $firstName = null, $lastName = null, $subscriber = null, $publication = null)
    {
        $users = $this->findBy(array('email' => $email));
        if (empty($users)) {
            $user = new User($email);
            $user->setPublic(true);
        } else {
            $user = $users[0];
        }

        if ($firstName) {
            $user->setFirstName($firstName);
        }

        if ($lastName) {
            $user->setLastName($lastName);
        }

        if ($subscriber) {
            $user->setSubscriber($subscriber);
        }

        if ($publication) {
            $user->setPublication($publication);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Save pending user
     *
     * @param array                $data
     * @param Newscoop\Entity\User $user
     *
     * @return void
     */
    public function savePending($data, User $user)
    {
        if (!$user->isPending()) {
            throw new \InvalidArgumentException("User '{$user->getUsername()}' is not pending user.");
        }

        $user->setActive();
        $user->setPublic(true);
        $this->save($data, $user);

        return $this;
    }

    /**
     * Create new activated user
     *
     * @param string  $email
     * @param string  $password
     * @param string  $firstName
     * @param string  $lastName
     * @param integer $publication
     */
    public function createUser($email, $password, $username, $firstName = null, $lastName = null, $publication = 0, $public = true, $userTypes = array(), $isAdmin = false)
    {
        $users = $this->findBy(array('email' => $email));
        if (!empty($users)) {
            throw new \Newscoop\Exception\ResourcesConflictException("User with this email already exists");
        }

        $user = new User($email);
        $user->setPassword($password);
        $user->setUsername($username);
        $user->setPublic($public);
        $user->setActive();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPublication($publication);
        $user->setAdmin($isAdmin);

        foreach ($userTypes as $type) {
            $user->addUserType($this->em->getReference('Newscoop\Entity\User\Group', $type));
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Test if username is available
     *
     * @param string $username
     *
     * @return bool
     */
    public function checkUsername($username)
    {
        return $this->getRepository()->isUnique('username', $username);
    }

    /**
     * Find user by author
     *
     * @param int $authorId
     *
     * @return Newscoop\Entity\User|null
     */
    public function findByAuthor($authorId)
    {
        return $this->getRepository()->findOneBy(array(
            'author' => $authorId,
        ));
    }

    public function loadUserByUsername($username)
    {
        return $this->getRepository()->findOneBy(array(
            'username' => $username,
        ));
    }

    /**
     * Count all users
     *
     * @return int
     */
    public function countAll()
    {
        return $this->getRepository()->countAll();
    }

    /**
     * Count users by given criteria
     *
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria)
    {
        return $this->getRepository()->countBy($criteria);
    }

    /**
     * Get count of public users
     *
     * @return int
     */
    public function getPublicUserCount()
    {
        return $this->countBy(array(
            'status' => User::STATUS_ACTIVE,
            'is_public' => true,
        ));
    }

    /**
     * Get repository for user entity
     *
     * @return Newscoop\Entity\Repository\UserRepository
     */
    private function getRepository()
    {
        if (null === $this->repository) {
            $this->repository = $this->em->getRepository($this->getClassName());
        }

        return $this->repository;
    }

    /**
     * Get user entity class name
     *
     * @return string
     */
    public function getClassName()
    {
        return self::USER_ENTITY;
    }

    /**
     * Get user group options
     *
     * @return array
     */
    public function getGroupOptions()
    {
        $query = $this->em->getRepository('Newscoop\Entity\User\Group')
            ->createQueryBuilder('g')
            ->select('g.id, g.name')
            ->orderBy('g.id')
            ->getQuery();

        $groups = array();
        foreach ($query->getResult() as $row) {
            $groups[$row['id']] = $row['name'];
        }

        return $groups;
    }

    /**
     * Log in user
     *
     * @param Newscoop\Entity\User $user
     * @param string               $providerKey
     *
     * @return UsernamePasswordToken
     */
    public function loginUser(User $user, $providerKey)
    {
        $roles = $user->getRoles();
        $token = new UsernamePasswordToken($user, null, $providerKey, $roles);
        $this->security->setToken($token);

        return $token;
    }

    /**
     * Get user IP
     *
     * @return string
     */
    public function getUserIp()
    {
        return $this->userIp;
    }

    /**
     * Set user IP
     *
     * @param string $userIp User IP
     *
     * @return string
     */
    public function setUserIp($userIp = null)
    {
        $this->userIp = $userIp;

        return $this;
    }

    /**
     * Resolve user IP from provided data
     *
     * @param Request $request Request object
     *
     * @return string $userIp User IP
     */
    public function userIpResolver(Request $request)
    {
        $userIp = null;
        if (!is_null($request->server->get('HTTP_CLIENT_IP'))) {
            $userIp = $request->server->get('HTTP_CLIENT_IP');
        } elseif (!is_null($request->server->get('HTTP_X_FORWARDED_FOR'))) {
            $userIp = $request->server->get('HTTP_X_FORWARDED_FOR');
        } else {
            $userIp = $request->server->get('REMOTE_ADDR');
        }

        $this->setUserIp($userIp);

        return $userIp;
    }

    /**
     * Update user points
     *
     * @param  GenericEvent $event
     * @return void
     */
    public function updateUserPoints(GenericEvent $event)
    {
        $params = $event->getArguments();
        $user = null;
        $authorId = null;
        if (array_key_exists('user', $params)) {
            $user = $params['user'];
            if (is_numeric($params['user'])) {
                $user = $this->find($params['user']);
            }
        }

        if (array_key_exists('authorId', $params)) {
            $authorId = $params['authorId'];
        }

        if ($user || $authorId) {
            $this->getRepository()->setUserPoints($user, $authorId);
        }
    }

    public function extractCriteriaFromRequest($request)
    {
        $criteria = new \Newscoop\User\UserCriteria();

        if ($request->query->has('sorts')) {
            foreach ($request->get('sorts') as $key => $value) {
                $criteria->orderBy[$key] = $value == '-1' ? 'desc' : 'asc';
            }
        }

        if ($request->query->has('queries')) {
            $queries = $request->query->get('queries');

            if (array_key_exists('search', $queries)) {
                $criteria->query = $queries['search'];
            }

            if (array_key_exists('search_name', $queries)) {
                $criteria->query_name = $queries['search_name'];
            }

            if (array_key_exists('filter', $queries)) {
                if ($queries['filter'] == 'active') {
                    $criteria->lastLoginDays = 30;
                }

                if ($queries['filter'] == 'registered') {
                    $criteria->status = User::STATUS_ACTIVE;
                }

                if ($queries['filter'] == 'pending') {
                    $criteria->status = User::STATUS_INACTIVE;
                }

                if ($queries['filter'] == 'deleted') {
                    $criteria->status = User::STATUS_DELETED;
                }
            }

            if (array_key_exists('user-group', $queries)) {
                foreach ($queries['user-group'] as $key => $value) {
                    $criteria->groups[$key] = $value;
                }
            }
        }

        $criteria->maxResults = $request->query->get('perPage', 10);
        if ($request->query->has('offset')) {
            $criteria->firstResult = $request->query->get('offset');
        }

        return $criteria;
    }
}
