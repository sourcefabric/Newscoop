<?php

namespace Newscoop\NewscoopBundle\Security\Encoder;

use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class NewscoopPasswordEncoder implements PasswordEncoderInterface
{
    public function encodePassword($raw, $salt)
    {
        $user =  new \Newscoop\Entity\User();
        return implode($user::HASH_SEP, array(
            $user::HASH_ALGO,
            $salt,
            hash($user::HASH_ALGO, $salt . $raw),
        ));
    }

    public function isPasswordValid($encoded, $raw, $salt)
    {
        return $encoded === $this->encodePassword($raw, $salt);
    }

}