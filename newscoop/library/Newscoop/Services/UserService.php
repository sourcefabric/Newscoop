<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\User;

/**
 * User service
 */
class UserService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /** @var Zend_Auth */
    private $auth;

    /** @var Newscoop\Entity\User */
    private $currentUser;

    /**
     * @param Doctrine\ORM\EntityManager $em
     * @param Zend_Auth $auth
     */
    public function __construct(EntityManager $em, \Zend_Auth $auth)
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
     * Find by given criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     */
    public function findBy($criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
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
        if ($user === null) {
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

        $user->setStatus(User::STATUS_DELETED);
        $this->em->flush();
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

        $username = strtolower(trim($firstName) . '-' . trim($lastName));
        $username = preg_replace('~[^\\pL0-9_]+~u', '-', $username);
        $username = trim($username, "-");
        $username = iconv("utf-8", "us-ascii//TRANSLIT", $username);
        $username = strtolower($username);
        $username = preg_replace('~[^-a-z0-9_]+~', '', $username);
        $username = str_replace('-', '.', $username);

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
    public function createPending($email)
    {
        $user = new User($email);
        $user->setPublic(true);
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
     * Get repository for user entity
     *
     * @return Newscoop\Entity\Repository\UserRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\User');
    }
}
