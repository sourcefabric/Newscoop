<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2015 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\Services;

use Newscoop\Entity\Article;
use Newscoop\Entity\Playlist;
use Newscoop\Entity\PlaylistArticle;
use Doctrine\ORM\EntityManager;
use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 * Manage playlists
 */
class PlaylistsService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct($em, $dispatcher, $cacheService)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->cacheService = $cacheService;
    }

    /**
     * Get articles for playlist
     *
     * @param \Newscoop\Entity\Playlist $playlist
     */
    public function getPlaylistArticles(Playlist $playlist, $onlyPublished = true)
    {
        $playlistArticles = $this->em->getRepository('Newscoop\Entity\Playlist')
            ->articles($playlist, null, true, null, null, $onlyPublished, true)
            ->getResult();

        return $playlistArticles;
    }

    /**
     * Remove article from playlist
     *
     * @param Playlist $playlist
     * @param Article  $articleToRemove
     *
     * @return boolean
     */
    public function removePlaylistArticle($playlist, $articleToRemove)
    {
        $playlistArticle = $this->em->getRepository('Newscoop\Entity\PlaylistArticle')
            ->getPlaylistArticle($playlist, $articleToRemove)
            ->getOneOrNullResult();

        if ($playlistArticle) {
            $this->em->remove($playlistArticle);
            $this->em->flush();

            $this->reorderAfterRemove($playlist, $playlistArticle->getOrder());
        }

        return true;
    }

    /**
     * Add new related article to related articles container
     *
     * @param Article $article
     * @param Article $articleToAdd
     * @param integer $position
     *
     * @return boolean
     */
    public function addArticle($playlist, $articleToAdd, $position = false)
    {
        $playlistArticle = $this->em->getRepository('Newscoop\Entity\PlaylistArticle')
            ->getPlaylistArticle($playlist, $articleToAdd)
            ->getOneOrNullResult();

        if ($playlistArticle) {
            $this->positionPlaylistArticle($playlist, $playlistArticle, $position);

            return true;
        }

        $playlistArticle = new PlaylistArticle($playlist, $articleToAdd);
        $this->em->persist($playlistArticle);
        $this->em->flush();

        $this->positionPlaylistArticle($playlist, $playlistArticle, $position);

        return true;
    }

    private function reorderAfterRemove($playlist, $removedArticlePosition)
    {
        $this->initOrderOnPlaylist($playlist);

        try {
            $this->em->getConnection()->exec('LOCK TABLES playlist_article WRITE;');

            // move all bigger than old position up (-1)
            $this->em
                ->createQuery('UPDATE Newscoop\Entity\PlaylistArticle pa SET pa.order = pa.order-1 WHERE pa.order > :oldPosition')
                ->setParameter('oldPosition', $removedArticlePosition)
                ->execute();

            $this->em->getConnection()->exec('UNLOCK TABLES;');
        } catch (\Exception $e) {
            $this->em->getConnection()->exec('UNLOCK TABLES;');
        }

        return true;
    }

    private function initOrderOnPlaylist($playlist)
    {
        $articles = $this->getPlaylistArticles($playlist, false);

        $index = 0;
        foreach ($articles as $article) {
            if (is_int($article->getOrder()) && $article->getOrder() > 0 && $index == 0) {
                return;
            }
            $index++;

            $article->setOrder($index);
        }

        $this->em->flush();

        return true;
    }

    private function positionPlaylistArticle($playlist, $playlistArticle, $position)
    {
        $this->initOrderOnPlaylist($playlist);

        if ($position == false) {
            $position = 1;
        } else {
            $position = (int) $position;
        }

        try {
            $this->em->getConnection()->exec('LOCK TABLES playlist_article WRITE, playlist_article as p0_ WRITE;');

            // check if position isn't bigger that max one;
            $maxPosition = $this->em
                ->createQuery('SELECT COUNT(pa) FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.idPlaylist = :playlistId AND pa.order > 0 ORDER BY pa.order ASC')
                ->setParameter('playlistId', $playlist->getId())
                ->getSingleScalarResult();

            if ($position > ((int) $maxPosition)) {
                $position = (int) $maxPosition;
            }

            // get article - move to position 0
            $oldOrder = $playlistArticle->getOrder();
            $playlistArticle->setOrder(0);
            $this->em->flush();
            // it's not new element and we need to pull down bigger elements on it place
            if ($oldOrder > 0) {
                // move all bigger than old position up (-1)
                $this->em
                    ->createQuery('UPDATE Newscoop\Entity\PlaylistArticle pa SET pa.order = pa.order-1 WHERE pa.order > :oldPosition AND pa.idPlaylist = :playlistId')
                    ->setParameter('oldPosition', $oldOrder)
                    ->setParameter('playlistId', $playlist->getId())
                    ->execute();
            }

            // move all bigger than new position down (+1)
            $this->em
                ->createQuery('UPDATE Newscoop\Entity\PlaylistArticle pa SET pa.order = pa.order+1 WHERE pa.order >= :newPosition AND pa.idPlaylist = :playlistId')
                ->setParameter('newPosition', $position)
                ->setParameter('playlistId', $playlist->getId())
                ->execute();

            // move changed element from position 0 to new position
            $playlistArticle->setOrder($position);
            $this->em->flush();

            $this->removeLeftItems($playlist);

            $this->em->getConnection()->exec('UNLOCK TABLES;');
        } catch (\Exception $e) {
            $this->em->getConnection()->exec('UNLOCK TABLES;');
        }

        $this->dispatcher->dispatch('playlist.save', new GenericEvent($this, array(
            'id' => $playlist->getId()
        )));
        $this->cacheService->clearNamespace('boxarticles');
    }

    public function removeLeftItems($playlist)
    {
        if ($playlist->getMaxItems() != null) {
            $this->em
                ->createQuery('DELETE FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.order > :maxPosition AND pa.idPlaylist = :playlistId')
                ->setParameter('maxPosition', $playlist->getMaxItems())
                ->setParameter('playlistId', $playlist->getId())
                ->execute();
        }
    }
}
