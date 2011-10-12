<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */
namespace Newscoop\Entity;

/**
 * Playlist entity
 * @Entity
 * @Table(name="playlist_article",
 * 	uniqueConstraints={@UniqueConstraint(name="playlist_article", columns={"id_playlist", "article_no"})})
 */
class PlaylistArticle extends Entity
{
	/**
     * @Id @Column(name="id_playlist_article", type="integer")
     * @GeneratedValue
     * @var int
     */
    protected $id;

	/**
     * @Id
     * @Column(type="integer", name="id_playlist")
     * @var int
     */
    protected $idPlaylist;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Article")
	 * @JoinColumn(name="article_no", referencedColumnName="Number")
     * @var Newscoop\Entity\Article
     */
    private $article;

    public function setPlaylist($idPlaylist)
    {
        $this->idPlaylist = $idPlaylist;
        return $this;
    }

    public function setArticle(Article $article)
    {
        $this->article = $article;
        return $this;
    }

    public function __toString(){ return 'playlist_article'; }
}