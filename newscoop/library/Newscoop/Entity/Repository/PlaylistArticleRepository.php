<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Newscoop\Entity\PlaylistArticle;
use Newscoop\Entity\Playlist;
use Doctrine\ORM\EntityRepository;
use Newscoop\Entity\Article;

class PlaylistArticleRepository extends EntityRepository
{
    /**
     * Delete playlist
     * @param Newscoop\Entity\Playlist $playlist
     */
    public function onPlaylistDelete(Newscoop\Entity\Playlist $playlist)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery("DELETE FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.playlist = ?1");
        $query->setParameter(1, $playlist);
        try
        {
            $query->execute();
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
            // TODO log here
            return array();
        }
        $rows = $query->getResult();
        return $rows;
    }

    /**
     * Delete article from playlists
     * @param int $articleId
     */
    public function deleteArticle($articleId)
    {
        $em = $this->getEntityManager();
        // $article = $em->getRepository('Newscoop\Entity\Article')->findOneBy(array('number' => $articleId, 'language' => $p_language));
        $query = $em->createQuery("DELETE FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.article = ?1");
        $query->setParameters( array('1' => $articleId));
        try
        {
            $query->execute();
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
            // TODO log here
            return array();
        }
        $rows = $query->getResult();
        return $rows;
    }
}
