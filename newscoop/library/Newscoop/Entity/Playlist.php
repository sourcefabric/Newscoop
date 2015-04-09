<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

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
     * @ORM\Column(length=1024, name="notes")
     * @var string
     */
    protected $notes;

    /**
     * @ORM\Column(type="integer", name="max_items")
     * @var int
     */
    protected $maxItems;

    /**
     * @ORM\Column(type="datetime", name="modification_time", nullable=true)
     * @var datetime
     */
    protected $articlesModificationTime;

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

    /**
     * Gets the value of notes.
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Sets the value of notes.
     *
     * @param string $notes the notes
     *
     * @return self
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Gets the value of maxItems.
     *
     * @return int
     */
    public function getMaxItems()
    {
        return $this->maxItems;
    }

    /**
     * Sets the value of maxItems.
     *
     * @param int $maxItems the max items
     *
     * @return self
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = $maxItems;

        return $this;
    }

    /**
     * Gets the value of articlesModificationTime.
     *
     * @return int
     */
    public function getArticlesModificationTime()
    {
        return $this->articlesModificationTime;
    }

    /**
     * Sets the value of articlesModificationTime.
     *
     * @param int $articlesModificationTime the articles modification time
     *
     * @return self
     */
    public function setArticlesModificationTime(\DateTime $articlesModificationTime)
    {
        $this->articlesModificationTime = $articlesModificationTime;

        return $this;
    }
}
