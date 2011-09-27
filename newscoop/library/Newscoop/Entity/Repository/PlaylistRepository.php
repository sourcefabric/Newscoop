<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Newscoop\Entity\PlaylistArticle,
    Newscoop\Entity\Language,
    Newscoop\Entity\Playlist,
    Doctrine\ORM\EntityRepository,
    Newscoop\Entity\Theme,
    Newscoop\Entity\Theme\Loader,
    Newscoop\Entity\Article;

class PlaylistRepository extends EntityRepository
{
    public function getArticles(Playlist $playlist, Language $lang = null)
    {
        $em = $this->getEntityManager();
        $query = $em->createQuery
        ("
        	SELECT a.number articleId, a.name title, a.date date
        	FROM Newscoop\Entity\PlaylistArticle pa
        	JOIN pa.article a
        	WHERE pa.idPlaylist = ?1".
           (is_null($lang) ? "GROUP BY a.number" : "AND a.language = ?2")
        );
        $query->setParameter(1, $playlist->getId());
        if (!is_null($lang)) {
            $query->setParameter(2, $lang->getId());
        }
        $rows = $query->getResult();
        return $rows;
    }

    public function save(Playlist $playlist = null, $articles = null)
    {
        $em = $this->getEntityManager();
        try
        {
            $em->persist($playlist);
            if (is_null($playlist->getId())) {
                $em->flush();
            }

            $em->getConnection()->beginTransaction();

            $query = $em->createQuery("DELETE FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.idPlaylist = ?1");
            $query->setParameter(1, $playlist->getId());
            $query->execute();

            if (!is_null($articles) && is_array($articles))
            {
                foreach ($articles as $articleId)
                {
                    $article = new PlaylistArticle();
                    $article->setId(array($playlist->getId(),$articleId));
                    $ar = $this->getEntityManager()->getRepository('Newscoop\Entity\Article');
                    if (($a = current($ar->findBy(array("number" => $articleId)))) instanceof \Newscoop\Entity\Article) {
                        $article->setArticle($a);
                    }
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
            return false;
        }
        return $playlist;
    }
}