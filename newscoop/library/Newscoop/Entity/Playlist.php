<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Playlist entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\PlaylistRepository")
 * @ORM\Table(name="playlist")
 */
class Playlist extends AbstractEntity
{
	/**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id_playlist")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(length=256, name="name")
     * @var string
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Newscoop\Entity\PlaylistArticle", mappedBy="playlist", cascade={"all"})
     */
    protected $articles;

    /**
     * Link to topic articles resource
     * @var string
     */
    protected $articlesLink;
    
    /**
     * Set link to topic articles resource
     * @param string $articlesLink Link to topic articles resource
     */
    public function setArticlesLink($articlesLink)
    {
        $this->articlesLink = $articlesLink;

        return $this;
    }

    /**
     * Get link to topic articles resource
     * @return string Link to topic articles resource
     */
    public function getArticlesLink()
    {
        return $this->articlesLink;
    }

    /**
     * Returns the name of the playlist
     */
    public function getName()
	{
		return $this->name;
	}

	/**
     * Returns the name of the playlist
     * @var string $name
     */
    public function setName($name)
	{
	    $this->name = $name;
		return $this;
	}
}
