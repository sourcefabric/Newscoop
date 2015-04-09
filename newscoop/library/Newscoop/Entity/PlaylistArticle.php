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
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\PlaylistArticleRepository")
 * @ORM\Table(name="playlist_article",
 * 	uniqueConstraints={@ORM\UniqueConstraint(name="playlist_article", columns={"id_playlist", "article_no"})})
 */
class PlaylistArticle extends AbstractEntity
{
	/**
     * @ORM\Id @ORM\Column(name="id_playlist_article", type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

	/**
     * @ORM\Column(type="integer", name="id_playlist")
     * @var int
     */
    protected $idPlaylist;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Playlist", inversedBy="articles")
	 * @ORM\JoinColumn(name="id_playlist", referencedColumnName="id_playlist")
     * @var Newscoop\Entity\Playlist
     */
    protected $playlist;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Article")
	 * @ORM\JoinColumn(name="article_no", referencedColumnName="Number")
     * @var Newscoop\Entity\Article
     */
    protected $article;

    /**
     * @ORM\Column(type="integer", name="article_no")
     * @var int
     */
    protected $articleNumber;

    /**
     * @ORM\Column(type="integer", name="order_number")
     * @var integer
     */
    protected $order;

    public function __construct($playlist, $article){
        $this->setPlaylist($playlist);
        $this->setArticle($article);
        $this->setOrder(0);
    }

    /**
     * set playlist
     * @return Newscoop\Entity\PlaylistArticle
     */
    public function setPlaylist(Playlist $playlist)
    {
        $this->playlist = $playlist;
        return $this;
    }

	/**
     * get playlist
     * @return Newscoop\Entity\Playlist
     */
    public function getPlaylist()
    {
        return $this->playlist;
    }

    /**
     * set article
     * @return Newscoop\Entity\PlaylistArticle
     */
    public function setArticle(Article $article)
    {
        $this->article = $article;
        return $this;
    }

	/**
     * get article
     * @return Newscoop\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Gets the value of order.
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets the value of order.
     *
     * @param integer $order the order
     *
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    public function __toString(){ return 'playlist_article'; }
}
