<?php

/**
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Playlist entity.
 *
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\PlaylistArticleRepository")
 * @ORM\Table(name="playlist_article")
 */
class PlaylistArticle extends AbstractEntity
{
    /**
     * @ORM\Id @ORM\Column(name="id_playlist_article", type="integer")
     * @ORM\GeneratedValue
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="id_playlist")
     *
     * @var int
     */
    protected $idPlaylist;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Playlist")
     * @ORM\JoinColumn(name="id_playlist", referencedColumnName="id_playlist")
     *
     * @var Newscoop\Entity\Playlist
     */
    protected $playlist;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Article")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="article_no", referencedColumnName="Number"),
     *     @ORM\JoinColumn(name="article_language", referencedColumnName="IdLanguage")
     * })
     *
     * @var Newscoop\Entity\Article
     */
    protected $article;

    /**
     * @ORM\Column(type="integer", name="article_no")
     *
     * @var int
     */
    protected $articleNumber;

    /**
     * @ORM\Column(type="integer", name="article_language")
     *
     * @var int
     */
    protected $articleLanguage;

    /**
     * @ORM\Column(type="integer", name="order_number")
     *
     * @var int
     */
    protected $order;

    public function __construct($playlist, $article)
    {
        $this->setPlaylist($playlist);
        $this->setArticle($article);
        $this->setOrder(0);
    }

    /**
     * set playlist.
     *
     * @return Newscoop\Entity\PlaylistArticle
     */
    public function setPlaylist(Playlist $playlist)
    {
        $this->playlist = $playlist;

        return $this;
    }

    /**
     * get playlist.
     *
     * @return Newscoop\Entity\Playlist
     */
    public function getPlaylist()
    {
        return $this->playlist;
    }

    /**
     * Gets the value of order.
     *
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Sets the value of order.
     *
     * @param int $order the order
     *
     * @return self
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    public function __toString()
    {
        return 'playlist_article';
    }

    /**
     * Gets the article.
     *
     * @return Newscoop\Entity\Article
     */
    public function getArticle()
    {
        return $this->article;
    }

    /**
     * Sets the article.
     *
     * @param Newscoop\Entity\Article $article the article
     *
     * @return self
     */
    public function setArticle(\Newscoop\Entity\Article $article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Gets the value of articleNumber.
     *
     * @return int
     */
    public function getArticleNumber()
    {
        return $this->articleNumber;
    }

    /**
     * Sets the value of articleNumber.
     *
     * @param int $articleNumber the article number
     *
     * @return self
     */
    public function setArticleNumber($articleNumber)
    {
        $this->articleNumber = $articleNumber;

        return $this;
    }

    /**
     * Gets the value of articleLanguage.
     *
     * @return int
     */
    public function getArticleLanguage()
    {
        return $this->articleLanguage;
    }

    /**
     * Sets the value of articleLanguage.
     *
     * @param int $articleLanguage the article language
     *
     * @return self
     */
    public function setArticleLanguage($articleLanguage)
    {
        $this->articleLanguage = $articleLanguage;

        return $this;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idPlaylist
     *
     * @param integer $idPlaylist
     * @return PlaylistArticle
     */
    public function setIdPlaylist($idPlaylist)
    {
        $this->idPlaylist = $idPlaylist;

        return $this;
    }

    /**
     * Get idPlaylist
     *
     * @return integer 
     */
    public function getIdPlaylist()
    {
        return $this->idPlaylist;
    }
}
