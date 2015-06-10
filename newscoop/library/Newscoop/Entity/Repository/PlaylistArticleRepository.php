<?php

/**
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\Entity\Repository;

use Newscoop\Entity\Playlist;
use Newscoop\Entity\Article;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

class PlaylistArticleRepository extends SortableRepository
{
    /**
     * Delete playlist.
     *
     * @param Newscoop\Entity\Playlist $playlist
     */
    public function onPlaylistDelete(Newscoop\Entity\Playlist $playlist)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("DELETE FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.playlist = ?1");
        $query->setParameter(1, $playlist);
        try {
            $query->execute();
        } catch (\Exception $e) {
            echo $e->getMessage();
            // TODO log here
            return array();
        }
        $rows = $query->getResult();

        return $rows;
    }

    /**
     * Delete article from playlists.
     *
     * @param int $articleId
     */
    public function deleteArticle($articleId, $languageId)
    {
        $em = $this->getEntityManager();
        $em->getConnection()->beginTransaction();

        $article = $em->getRepository('Newscoop\Entity\PlaylistArticle')
            ->findOneBy(array(
                'articleNumber' => $articleId,
                'articleLanguage' => $languageId,
        ));

        try {
            $em->remove($article);
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->close();

            return $e;
        }

        return $article;
    }

    public function getPlaylistArticle($playlist, $article)
    {
        $em = $this->getEntityManager();
        $queryBuilder = $em->getRepository('Newscoop\Entity\PlaylistArticle')
            ->createQueryBuilder('pa');

        $queryBuilder->where('pa.playlist = :playlist')
            ->andWhere('pa.articleNumber = :articleNumber')
            ->andWhere('pa.articleLanguage = :articleLanguage')
            ->setParameters(array(
                'playlist' => $playlist,
                'articleNumber' => $article->getNumber(),
                'articleLanguage' => $article->getLanguageId(),
            ));

        $query = $queryBuilder->getQuery();

        return $query;
    }
}
