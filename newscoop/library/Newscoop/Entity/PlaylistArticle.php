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
 * @Table(name="playlist_article",uniqueConstraints={@UniqueConstraint(name="playlist_article", columns={"id_article", "article_no"})})
 */
class PlaylistArticle
{
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

    public function setId($idPair)
    {
        $this->idPlaylist = current($idPair);
        $this->articleNo = next($idPair);
    }

    public function setArticle($article)
    {
        $this->article = $article;
        return $this;
    }
}