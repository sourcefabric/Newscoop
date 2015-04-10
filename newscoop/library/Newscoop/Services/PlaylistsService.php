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

        $this->clearPlaylistTemplates($playlist);

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
        if ($position == false) {
            $position = 1;
        } else {
            $position = (int) $position;
        }

        try {
            $this->em->getConnection()->exec('LOCK TABLES playlist_article WRITE, playlist_article as p0_ WRITE;');

            // check if position isn't bigger that max one;
            $maxPosition = $this->em
                ->createQuery('SELECT COUNT(pa) FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.idPlaylist = :playlistId AND pa.order >= 0 ORDER BY pa.order ASC')
                ->setParameter('playlistId', $playlist->getId())
                ->getSingleScalarResult();

            if ($position > ((int) $maxPosition)) {
                $position = (int) $maxPosition;
            }

            // get article - move to position 0
            $oldOrder = $playlistArticle->getOrder();
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
            'id' => $playlist->getId(),
        )));
        $this->cacheService->clearNamespace('boxarticles');
        $this->clearPlaylistTemplates($playlist);
    }

    /**
     * Remove items above the limit on playlist
     *
     * @param Playlist $playlist
     */
    public function removeLeftItems($playlist)
    {
        if ($playlist->getMaxItems() != null) {
            $this->em
                ->createQuery('DELETE FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.order > :maxPosition AND pa.idPlaylist = :playlistId')
                ->setParameter('maxPosition', $playlist->getMaxItems() + 1)
                ->setParameter('playlistId', $playlist->getId())
                ->execute();
        }
    }

    /**
     * Load articlesLists from xml file
     *
     * example template articles lists schema:
     *
     * <articlesLists> # main section
     *   <list name="FrontPage"> # single playlist declaration
     *     <template file="article.tpl" /> # single template file assigned to playlist declaration
     *   </list>
     *   <list name="Second Playlist">
     *     <template file="issue.tpl" />
     *     <template file="front.tpl" />
     *   </list>
     * </articlesLists>
     *
     * Cache for all assigned to playlist templates will be cleared after playlist update action
     *
     * @param string $path
     * 
     * @return array content of xml file as an array
     */
    public function loadThemePlaylists($path)
    {
        $xml = simplexml_load_file($path);

        return json_decode(json_encode($xml->articlesLists), true);
    }

    /**
     * Check if playlists have current theme lists definitions
     *
     * @param Theme $theme
     * @param array $themePlaylists
     *
     * @return boolean
     */
    public function checkIfThemePlaylistsAreUpToDate($theme, $themePlaylists)
    {
        $newThemePlaylists = $this->buildNewThemePlaylists($themePlaylists);

        foreach ($newThemePlaylists as $playlistName => $themePlaylist) {
            $playlist = $this->em->getRepository('Newscoop\Entity\Playlist')->getPlaylistByTitle($playlistName)->getOneOrNullResult();
            if (!$playlist) {
                return false;
            }
            
            $themes = $playlist->getThemes();
            if (!array_key_exists($theme->getId(), $themes)) {
                return false;
            }

            foreach($themePlaylist['templates'] as $template) {
                if (!in_array($template, $themes[$theme->getId()])) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Update playists with current theme playlists definitions
     *
     * @param Theme $theme
     * @param array $themePlaylists
     *
     * @return boolean
     */
    public function updateThemePlaylists($theme, $themePlaylists)
    {
        $newThemePlaylists = $this->buildNewThemePlaylists($themePlaylists);

        foreach ($newThemePlaylists as $playlistName => $themePlaylist) {
            $playlist = $this->em->getRepository('Newscoop\Entity\Playlist')->getPlaylistByTitle($playlistName)->getOneOrNullResult();
            if (!$playlist) {
                $playlist = new Playlist();
                $playlist->setName($playlistName);

                $this->em->persist($playlist);
            }

            $themes = $playlist->getThemes();
            $themes[$theme->getId()] = $themePlaylist['templates'];

            $playlist->setThemes($themes);
        }

        $this->em->flush();

        return true;
    }

    /**
     * Clear playlists themes definitions on unassign action
     *
     * @param Theme $theme
     * @param array $themePlaylists
     * 
     * @return boolean
     */
    public function removeThemeFromPlaylists($theme, $themePlaylists)
    {
        $newThemePlaylists = $this->buildNewThemePlaylists($themePlaylists);

        foreach ($newThemePlaylists as $playlistName => $themePlaylist) {
            $playlist = $this->em->getRepository('Newscoop\Entity\Playlist')->getPlaylistByTitle($playlistName)->getOneOrNullResult();
            if (!$playlist) {
                continue;
            }

            $themes = $playlist->getThemes();
            unset($themes[$theme->getId()]);

            $playlist->setThemes($themes);
        }

        $this->em->flush();

        return true;
    }

    /**
     * Clear cache for all temlates assigned to playlist
     *
     * @param Playlist $playlist
     */
    public function clearPlaylistTemplates($playlist)
    {
        foreach($playlist->getThemes() as $theme) {
            foreach($theme as $file) {
                \TemplateCacheHandler_DB::clean($file);
            }
        }
    }

    private function buildNewThemePlaylists($themePlaylists)
    {
        $newThemePlaylists = array();
        if (array_key_exists('template', $themePlaylists['list'])) {
            $bakThemePlaylists = $themePlaylists;
            $themePlaylists = array();
            $themePlaylists['list'][0] = $bakThemePlaylists['list'];
        }

        foreach($themePlaylists['list'] as $themePlaylist) {
            $newThemePlaylists[$themePlaylist['@attributes']['name']] = array();
            if (array_key_exists('@attributes', $themePlaylist['template'])) {
                $bakThemePlaylist = $themePlaylist;
                $themePlaylist = array();
                $themePlaylist['template'][0] = $bakThemePlaylist['template'];
            }

            foreach($themePlaylist['template'] as $template) {
                $newThemePlaylists[$themePlaylist['@attributes']['name']]['templates'][] = $template['@attributes']['file'];
            }
        }

        return $newThemePlaylists;
    }
}
