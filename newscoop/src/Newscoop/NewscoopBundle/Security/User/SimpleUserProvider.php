<?php

namespace Newscoop\NewscoopBundle\Security\User;

use Newscoop\Entity\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class SimpleUserProvider implements UserProviderInterface
{
    protected $users;

    protected $em;

    protected $config;

    public function __construct($em, array $config)
    {
        $this->em = $em;
        $this->config = $config;
        if (file_exists($this->config['users_file'])) {
            $this->users = json_decode(file_get_contents($this->config['users_file']), true);
        } else {
            $this->users = array();
        }
    }

    public function loadUserByUsername($username)
    {
        if (isset($this->users[$username])) {
            // query User entity
            $user = $this->em->getRepository('Newscoop\Entity\User')->findOneByUsername($username);

            // create the user if they don't already exist
            if (empty($user)) {
                // create user
                $user = new User();
                $this->em->getRepository('Newscoop\Entity\User')->save($user, array(
                    'username' => $username,
                    'email' => $username,
                    'password' => $this->users[$username],
                    'status' => 1,
                    'is_admin' => 1,
                    'is_verified' => 1,
                    'is_featured' => 0,
                    'user_type' => array('4'), // set as Journalist
                    'attributes' => array()
                ));
                return $user;
            } else {
                // or return the user
                return $user;
            }
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return 'Newscoop\Entity\User' === $class;
    }
}
