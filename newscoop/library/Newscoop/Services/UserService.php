<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\Common\Persistence\ObjectManager,
    Newscoop\Entity\User,
    Newscoop\Entity\UserAttribute;

/**
 * User service
 */
class UserService
{
    /** @var array */
    private $config = array();

    /** @var Doctrine\Common\Persistence\ObjectManager */
    private $em;

    /** @var Zend_Auth */
    private $auth;

    /** @var Newscoop\Entity\User */
    private $currentUser;

    /** @var Newscoop\Entity\Repository\UserRepository */
    private $repository;

    /** @var array */
    private $attributes = array(
        'birth_date',
        'facebook',
        'google',
        'twitter',
        'website',
        'bio',
        'email_public',
        'customer_id',
    );

    /**
     * @param array $config
     * @param Doctrine\ORM\EntityManager $em
     * @param Zend_Auth $auth
     */
    public function __construct(array $config, ObjectManager $em, \Zend_Auth $auth)
    {
        $this->config = $config;
        $this->em = $em;
        $this->auth = $auth;
        $this->repository = $this->em->getRepository('Newscoop\Entity\User');
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
                $this->currentUser = $this->repository->find($this->auth->getIdentity());
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
        return $this->repository->find($id);
    }

    /**
     * Find all users
     *
     * @return mixed
     */
    public function findAll()
    {
        return $this->repository->findAll();
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
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Find one by given criteria
     *
     * @param array $criteria
     * @return Newscoop\Entity\User
     */
    public function findOneBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
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

        $this->repository->save($user, $data);
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

        $this->repository->delete($user);
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
            $conflict = $this->repository->findOneBy(array(
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

        $user->addAttribute(UserAttribute::HTTP_USER_AGENT, $_SERVER['HTTP_USER_AGENT']);
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
        return $this->repository->isUnique('username', $username);
    }

    /**
     * Find user by author
     *
     * @param int $authorId
     * @return Newscoop\Entity\User|null
     */
    public function findByAuthor($authorId)
    {
        return $this->repository->findOneBy(array(
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
        return $this->repository->countAll();
    }

    /**
     * Count users by given criteria
     *
     * @param array $criteria
     * @return int
     */
    public function countBy(array $criteria)
    {
        return $this->repository->countBy($criteria);
    }

    /**
     * Count public users
     *
     * @return int
     */
    public function countPublicUsers()
    {
        return $this->countBy(array(
            'status' => User::STATUS_ACTIVE,
            'is_public' => true,
        ));
    }

    /**
     * Find public users
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findPublicUsers($limit, $offset)
    {
        return $this->findBy(array(
            'status' => User::STATUS_ACTIVE,
            'is_public' => true,
        ), array('username' => 'asc'), (int) $limit, (int) $offset);
    }
    
    /**
     * Get random list of users
     *
     * @param int $limit
     * @return array
     */
    public function getRandomList($limit = 25)
    {
        return $this->repository->getRandomList($limit);
    }
    
    /**
     * List editors
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findEditors($limit = NULL, $offset = NULL)
    {
        return $this->repository->findEditors($this->config['editorRoles'], $limit, $offset);
    }

    /**
     * Get editors count
     *
     * @return int
     */
    public function getEditorsCount()
    {
        return $this->repository->getEditorsCount($this->config['editorRoles']);
    }

    /**
     * Check if user is an editor
     *
     * @param MetaUser $user
     * @return bool
     */
    public function isEditor($user)
    {
        foreach ($this->findEditors() as $editor) {
            if ($user->getId() == $editor->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * List active users
     *
     * @return array
     */
    public function getActiveUsers($countOnly=false, $page=1, $limit=8)
    {
        $offset = ($page - 1) * $limit;
        return $this->repository->findActiveUsers($countOnly, $offset, $limit, $this->config['communityExcludedRoles']);
    }

    public function getVerifiedUsers($countOnly=false, $page=1, $limit=8)
    {
        $offset = ($page - 1) * $limit;
        return $this->repository->findVerifiedUsers($countOnly, $offset, $limit);
    }

    /**
     * Find user by string
     *
     * @return array
     */
    public function findUsersBySearch($search, $countOnly=false, $page=1, $limit=25)
    {
        $offset = ($page-1) * $limit;
        $result = $this->repository->searchUsers($search, $countOnly, $offset, $limit);
        return $result;
    }

    /**
     * Find users by first character of username
     *
     * @param string $character
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findByUsernameFirstCharacter($character, $limit = 25, $offset = 0)
    {
        return $this->repository->findByUsernameFirstCharacterIn($this->getCharacters($character), $limit, $offset);
    }

    /**
     * Count users by first character of username
     *
     * @param string $character
     * @return int
     */
    public function countByUsernameFirstCharacter($character)
    {
        return $this->repository->countByUsernameFirstCharacterIn($this->getCharacters($character));
    }

    /**
     * Get user posts count
     *
     * @param Newscoop\Entity\User $user
     * @return int
     */
    public function getUserPostsCount(User $user)
    {
        return $this->repository->getUserPostsCount($user);
    }

    /**
     * Get characters for given character group
     *
     * @param string $character
     * @return array
     */
    private function getCharacters($character)
    {
        $character = strtolower($character);
        return isset($this->config['characterGroup'][$character]) ?
            explode(' ', $this->config['characterGroup'][$character]) : array($character);
    }

    /**
     * Get newscoop login count
     *
     * @return int
     */
    public function getNewscoopLoginCount()
    {
        return $this->repository->getNewscoopLoginCount();
    }

    /**
     * Get facebook login count
     *
     * @return int
     */
    public function getFacebookLoginCount()
    {
        return $this->repository->getExternalLoginCount();
    }

    /**
     * Update user profile
     *
     * @param array $values
     * @param Newscoop\Entity\User $user
     * @return void
     */
    public function updateProfile(array $values, User $user)
    {
        if (isset($values['first_name'])) {
            $user->setFirstName($values['first_name']);
        }

        if (isset($values['last_name'])) {
            $user->setLastName($values['last_name']);
        }

        if (isset($values['image'])) {
            $user->setImage($values['image']);
        }

        foreach ($this->attributes as $attribute) {
            if (isset($values[$attribute])) {
                $user->addAttribute($attribute, $values[$attribute]);
            }
        }

        $this->em->flush();
    }

    /**
     * Get user points
     *
     * @param Newscoop\Entity\User $user
     * @return int
     */
    public function getUserPoints(User $user)
    {
        if ($user->getPoints() === null) {
            $this->em->getRepository('Newscoop\Entity\User')->getUserPoints($user);
        }

        return $user->getPoints();
    }
}
