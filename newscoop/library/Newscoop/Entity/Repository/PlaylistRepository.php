<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Newscoop\Entity\PlaylistArticle;
use Newscoop\Entity\Language;
use Newscoop\Entity\Playlist;
use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\Article;

class PlaylistRepository extends EntityRepository
{

    public function getPlaylists()
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->getRepository('Newscoop\Entity\Playlist')
            ->createQueryBuilder('p');

        $query = $queryBuilder->getQuery();
        
        return $query;
    }

    /**
     * Returns articles for a given playlist
     * @param Playlist $playlist
     * @param Language $lang
     * @param bool $fullArticle
     * @param int $limit
     * @param int $offset
     * @param bool $publishedOnly
     */
    public function articles(Playlist $playlist, Language $lang = null,
    $fullArticle = false, $limit = null, $offset = null, $publishedOnly = true, $onlyQuery = false)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT ".( $fullArticle ? "pa, a" : "a.number articleId, a.name title, a.updated date" )
        .   " FROM Newscoop\Entity\PlaylistArticle pa
            JOIN pa.article a
            WHERE pa.playlist = ?1 "
        .       ($publishedOnly ? " AND a.workflowStatus = 'Y'" : "")
        .       (is_null($lang) ? " GROUP BY a.number" : " AND a.language = ?2")
        .       " ORDER BY pa.id "
        );

        $query->setParameter(1, $playlist);
        if (!is_null($lang)) {
            $query->setParameter(2, $lang->getId());
        }

        if ($onlyQuery) {
            return $query;
        }

        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }
        if (!is_null($offset)) {
            $query->setFirstResult($offset);
        }

        $rows = $query->getResult();
        return $rows;
    }

    /**
     * Returns the total count of articles for a given playlist
     * @param Playlist $playlist
     * @param Language $lang
     * @param bool $publishedOnly
     */
    public function articlesCount(Playlist $playlist, Language $lang = null, $publishedOnly = true)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("
            SELECT COUNT(DISTINCT pa.article) FROM Newscoop\Entity\PlaylistArticle pa
            JOIN pa.article a
            WHERE pa.playlist = ?1 "
        .       ($publishedOnly ? " AND a.workflowStatus = 'Y'" : "")
        .       (is_null($lang) ? "" : " AND a.language = ?2")
        .       " ORDER BY pa.id"
        );

        $query->setParameter(1, $playlist);
        if (!is_null($lang)) {
            $query->setParameter(2, $lang->getId());
        }

        $count = $query->getSingleScalarResult();
        return $count;
    }

    /**
     * Gets the list of playlist the given article belongs to.
     *
     * @param int $articleId
     * @return array
     */
    public function getArticlePlaylists($articleId)
    {
        $playlistArticles = $this->getEntityManager()->getRepository('Newscoop\Entity\PlaylistArticle')
            ->findBy(array('article' => $articleId));

        $playlists = array();
        foreach ((array) $playlistArticles as $playlistArticle) {
            $playlists[] = $playlistArticle->getPlaylist();
        }

        return $playlists;
    }

    /**
     * Save playlist with articles
     * @param Playlist $playlist $playlist
     * @param array $articles
     */
    public function save(Playlist $playlist = null, $articles = null)
    {
        $em = $this->getEntityManager();

        try
        {
            $em->getConnection()->beginTransaction();

            $em->persist($playlist);
            if (is_null($playlist->getId())) {
                $em->flush();
            }

            $query = $em->createQuery("DELETE FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.playlist = ?1");
            $query->setParameter(1, $playlist);
            $query->execute();

            if (!is_null($articles) && is_array($articles)) {
                $ar = $this->getEntityManager()->getRepository('Newscoop\Entity\Article');
                foreach ($articles as $articleId)
                {
                    $article = new PlaylistArticle();
                    $article->setPlaylist($playlist);
                    if (($a = current($ar->findBy(array("number" => $articleId)))) instanceof \Newscoop\Entity\Article) {
                        $article->setArticle($a);
                    }
                    //$em->getConnection()->executeUpdate("INSERT INTO playlist_article(id_playlist, article_no) VALUES(?, ?)", array( $playlist->getId(), $a->getId()));
                    $em->persist($article);
                }
            }
            $em->flush();
            $em->getConnection()->commit();
        }
        catch (\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->close();
            return $e;
        }
        return $playlist;
    }

    /**
     * Delete playlist
     * @param Playlist $playlist
     */
    public function delete(Playlist $playlist)
    {
        if (!$playlist) {
            return false;
        }
        $em = $this->getEntityManager();
        $em->remove($playlist);
        $em->flush();
    }
}