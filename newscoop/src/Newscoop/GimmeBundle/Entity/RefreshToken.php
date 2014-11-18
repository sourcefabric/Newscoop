<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Entity;

use FOS\OAuthServerBundle\Entity\RefreshToken as BaseRefreshToken;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="OAuthRefreshToken")
 * @ORM\Entity()
 */
class RefreshToken extends BaseRefreshToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Newscoop\GimmeBundle\Entity\Client")
     * @ORM\JoinColumn(nullable=false)
     * @var Newscoop\GimmeBundle\Entity\Client
     */
    protected $client;

    /**
     * @ORM\ManyToOne(targetEntity="\Newscoop\Entity\Publication")
     * @ORM\JoinColumn(name="IdPublication", referencedColumnName="Id")
     * @var Newscoop\Entity\Publication
     */
    protected $publication;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\User")
     * @ORM\JoinColumn(referencedColumnName="Id")
     * @var UserInterface
     */
    protected $user;

    /**
     * Gets the value of user.
     *
     * @return \Newscoop\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets the value of user.
     *
     * @param \Newscoop\Entity\User $user the user
     *
     * @return self
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Gets the value of id.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of client.
     *
     * @return Newscoop\GimmeBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * Sets the value of client.
     *
     * @param Newscoop\GimmeBundle\Entity\Client $client the client
     *
     * @return self
     */
    public function setClient(\FOS\OAuthServerBundle\Model\ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Gets the value of publication.
     *
     * @return Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }
    
    /**
     * Sets the value of publication.
     *
     * @param \Newscoop\Entity\Publication $publication the publication
     *
     * @return self
     */
    public function setPublication(\Newscoop\Entity\Publication $publication)
    {
        $this->publication = $publication;

        return $this;
    }
}