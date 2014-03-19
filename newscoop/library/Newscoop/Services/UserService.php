<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Newscoop\Entity\User;
use Newscoop\Entity\UserAttribute;
use Newscoop\PaginatedCollection;
use InvalidArgumentException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

    /** @var Symfony\Component\Security\Core\SecurityContext */
    protected $security;

    /** @var Symfony\Component\Security\Core\Encoder\EncoderFactory */
    protected $factory;

    /** @var Symfony\Component\HttpFoundation\Request */
    protected $request;

    /** @var ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->em = $this->container->get('em');
        $this->auth = $this->container->get('zend_auth');
        $this->security = $this->container->get('security.context');
        $this->factory = $this->container->get('security.encoder_factory');
        $this->request = $this->container->get('request');
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
                    if( $this->security->isGranted('IS_AUTHENTICATED_FULLY') ){
                        $this->currentUser = $currentUser;
                    } else {
                        $this->currentUser = null;
                    }
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
        $qb = $this->repository->createQueryBuilder('u');
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        if (!empty($criteria['q'])) {
            $q = $qb->expr()->literal('%' . $criteria['q'] . '%');
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
        $user->setUsername(trim($firstName) . ' ' . trim($lastName));
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
    public function createPending($email, $firstName = null, $lastName = null, $subscriber = null)
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
     *
     * @return void
     */
    public function loginUser(User $user)
    {
        $providerKey = 'frontend_area';
        $roles = $user->getRoles();
        $token = new UsernamePasswordToken($user, null, $providerKey, $roles);
        $this->security->setToken($token);

        return $token;
    }

    /**
     *  Get user ip
     *
     * @return string
     */
    public function getIp()
    {
        if (!is_null($this->request->server->get('HTTP_CLIENT_IP'))) {
            return $this->request->server->get('HTTP_CLIENT_IP');
        } elseif (!is_null($this->request->server->get('HTTP_X_FORWARDED_FOR'))) {
            return $this->request->server->get('HTTP_X_FORWARDED_FOR');
        } else {
            return $this->request->server->get('REMOTE_ADDR');
        }
    }
}
