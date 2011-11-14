<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\Common\Persistence\ObjectManager,
    Newscoop\Entity\User,
    Newscoop\Persistence\ObjectRepository;

/**
 * User service
 */
class UserService implements ObjectRepository
{
    /** @var \Doctrine\Common\Persistence\ObjectManager */
    private $em;

    /** @var \Zend_Auth */
    private $auth;

    /** @var \Newscoop\Entity\User */
    private $currentUser;

    /** @var \Newscoop\Entity\Repository\UserRepository */
    private $repository;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Zend_Auth $auth
     */
    public function __construct(ObjectManager $em, \Zend_Auth $auth)
    {
        $this->em = $em;
        $this->auth = $auth;
    }

    /**
     * Get current user
     *
     * @return Newscoop\Entity\User
     */
    public function getCurrentUser()
    {
        if ($this->currentUser === NULL) {
            if ($this->auth->hasIdentity()) {
                $this->currentUser = $this->getRepository()
                    ->find($this->auth->getIdentity());
            }
        }

        return $this->currentUser;
    }

    /**
     * Find user
     *
     * @param int $id
     * @return Newscoop\Entity\User
     */
    public function find($id)
    {
        return $this->getRepository()
            ->find($id);
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
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return mixed
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Find one by given criteria
     *
     * @param array $criteria
     * @return Newscoop\Entity\User
     */
    public function findOneBy(array $criteria)
    {
        return $this->getRepository()
            ->findOneBy($criteria);
    }

    /**
     * Save user
     *
     * @param array $data
     * @param Newscoop\Entity\User $user
     * @return Newscoop\Entity\User
     */
    public function save(array $data, User $user = null)
    {
        if (NULL === $user) {
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
     * Generate username
     *
     * @param string $firstName
     * @param string $lastName
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
     * @param string $email
     * @return Newscoop\Entity\User
     */
    public function createPending($email, $first_name = null, $last_name = null, $subscriber = null)
    {
        $users = $this->findBy(array('email' => $email));
        if (empty($users)) {
            $user = new User($email);
            $user->setPublic(true);
        } else {
            $user = $users[0];
        }

        if ($first_name) {
            $user->setFirstName($first_name);
        }

        if ($last_name) {
            $user->setLastName($last_name);
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
     * @param array $data
     * @param Newscoop\Entity\User $user
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
    }

    /**
     * Test if username is available
     *
     * @param string $username
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
            $this->repository = $this->em->getRepository('Newscoop\Entity\User');
        }

        return $this->repository;
    }
}
