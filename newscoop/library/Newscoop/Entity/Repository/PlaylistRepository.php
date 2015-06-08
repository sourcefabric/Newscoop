<?php

/**
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

    public function getPlaylist($id)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->getRepository('Newscoop\Entity\Playlist')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id);

        $query = $queryBuilder->getQuery();

        return $query;
    }

    public function getPlaylistByTitle($title)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->getRepository('Newscoop\Entity\Playlist')
            ->createQueryBuilder('p')
            ->where('p.name = :title')
            ->setParameter('title', $title);

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Returns articles for a given playlist.
     *
     * @param Newscoop\Entity\Playlist $playlist
     * @param Newscoop\Entity\Language $lang
     * @param bool                     $fullArticle
     * @param int                      $limit
     * @param int                      $offset
     * @param bool                     $publishedOnly
     */
    public function articles(Playlist $playlist, array $languages = array(), $fullArticle = false, $limit = null, $offset = null, $publishedOnly = true, $onlyQuery = false, $orderBy = 'order')
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder();
        if (!$fullArticle) {
            $query->select('a.number articleId, l.id languageId, a.name title, a.updated date, a.workflowStatus workflowStatus, a.type type');
        } else {
            $query->select('p, a');
        }

        $query
            ->from('Newscoop\Entity\PlaylistArticle', 'p')
            ->join('p.article', 'a')
            ->join('a.language', 'l')
            ->where('p.playlist = ?1');

        if ($publishedOnly) {
            $query->andWhere('a.workflowStatus = \'Y\'');
        }

        $query->setParameter(1, $playlist);
        if (!empty($languages)) {
            $query
                ->andWhere('l.code IN(?2)')
                ->setParameter(2, $languages);
        } else {
            $query->groupBy('p.articleNumber, p.articleLanguage');
        }

        if (!is_null($limit)) {
            $query->setMaxResults($limit);
        }
        if (!is_null($offset)) {
            $query->setFirstResult($offset);
        }

        $query->orderBy("p.$orderBy");
        if ($onlyQuery) {
            return $query->getQuery();
        }

        $rows = $query->getQuery()->getArrayResult();

        return $rows;
    }

    /**
     * Returns the total count of articles for a given playlist.
     *
     * @param Newscoop\Entity\Playlist $playlist
     * @param Language                 $lang
     * @param bool                     $publishedOnly
     */
    public function articlesCount(Playlist $playlist, array $languages = array(), $publishedOnly = true)
    {
        $em = $this->getEntityManager();
        $query = $em->createQueryBuilder();
        $query
            ->select('count(distinct pa.articleNumber)')
            ->from('Newscoop\Entity\PlaylistArticle', 'pa')
            ->innerJoin('pa.article', 'a', 'WITH', 'pa.articleNumber = a.number')
            ->leftJoin('a.language', 'l')
            ->where('pa.playlist = ?1')
            ->orderBy('pa.id');

        if (!empty($languages)) {
            $query
                ->andWhere('l.code IN(?2)')
                ->setParameter(2, $languages);
        }

        if ($publishedOnly) {
            $query->andWhere('a.workflowStatus = \'Y\'');
        }

        $query->setParameter(1, $playlist);
        if (!is_null($lang)) {
            $query->setParameter(2, $lang->getId());
        }

        $count = $query->getQuery()->getSingleScalarResult();

        return $count;
    }

    /**
     * Gets the list of playlist the given article belongs to.
     *
     * @param int $articleId
     *
     * @return array
     */
    public function getArticlePlaylists($articleId, $languageId)
    {
        $playlistArticles = $this->getEntityManager()->getRepository('Newscoop\Entity\PlaylistArticle')
            ->findBy(array(
                'articleNumber' => $articleId,
                'articleLanguage' => $languageId,
            ));

        $playlists = array();
        foreach ((array) $playlistArticles as $playlistArticle) {
            $playlists[] = $playlistArticle->getPlaylist();
        }

        return $playlists;
    }

    /**
     * Save playlist with articles.
     *
     * @param Newscoop\Entity\Playlist $playlist $playlist
     * @param array                    $articles
     */
    public function save(Playlist $playlist = null, $articles = null)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();

        try {

            $em->persist($playlist);
            if (is_null($playlist->getId())) {
                $em->flush();
            }

            $query = $em->createQuery("DELETE FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.playlist = ?1");
            $query->setParameter(1, $playlist);
            $query->execute();

            if (!is_null($articles) && is_array($articles)) {
                $ar = $this->getEntityManager()->getRepository('Newscoop\Entity\Article');
                foreach ($articles as $articleId) {
                    $article = new PlaylistArticle();
                    $article->setPlaylist($playlist);
                    if (($a = current($ar->findBy(array('number' => $articleId)))) instanceof \Newscoop\Entity\Article) {
                        $article->setArticle($a);
                    }

                    $em->persist($article);
                }
            }
            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->close();

            return $e;
        }

        return $playlist;
    }

    /**
     * Delete playlist.
     *
     * @param Newscoop\Entity\Playlist $playlist
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
