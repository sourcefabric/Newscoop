<?php

/**
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
 * Manage playlists.
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
     * Get articles for playlist.
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
     * Remove article from playlist.
     *
     * @param Playlist $playlist
     * @param Article  $articleToRemove
     *
     * @return bool
     */
    public function removePlaylistArticle($playlist, $articleToRemove)
    {
        $playlistArticle = $this->em->getRepository('Newscoop\Entity\PlaylistArticle')
            ->getPlaylistArticle($playlist, $articleToRemove)
            ->getOneOrNullResult();

        if ($playlistArticle) {
            $this->em->remove($playlistArticle);
            $this->em->flush();
        }

        return true;
    }

    /**
     * Add new related article to related articles container.
     *
     * @param Article $article
     * @param Article $articleToAdd
     * @param int     $position
     *
     * @return bool
     */
    public function addArticle($playlist, $articleToAdd, $position = false)
    {
        $playlistArticle = $this->em->getRepository('Newscoop\Entity\PlaylistArticle')
            ->getPlaylistArticle($playlist, $articleToAdd)
            ->getOneOrNullResult();

        if (!$playlistArticle) {
            $playlistArticle = new PlaylistArticle($playlist, $articleToAdd);
            $this->em->persist($playlistArticle);
        }

        if ($position) {
            $playlistArticle->setOrder($position);
        }

        $this->em->flush();

        $this->removeLeftItems($playlist);

        return true;
    }

    /**
     * Remove items above the limit on playlist.
     *
     * @param Playlist $playlist
     */
    public function removeLeftItems($playlist)
    {
        if ($playlist->getMaxItems() != null) {
            $allowedArticles = $this->em
                ->createQuery('SELECT pa.id FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.idPlaylist = :playlistId AND pa.order >= 0 ORDER BY pa.order ASC')
                ->setParameter('playlistId', $playlist->getId())
                ->setMaxResults($playlist->getMaxItems())
                ->getArrayResult();

            $ids = array();
            foreach ($allowedArticles as $article) {
                $ids[] = $article['id'];
            }

            $this->em
                ->createQuery('DELETE FROM Newscoop\Entity\PlaylistArticle pa WHERE pa.id NOT IN (:ids) AND pa.idPlaylist = :playlistId')
                ->setParameter('ids', $ids)
                ->setParameter('playlistId', $playlist->getId())
                ->execute();
        }
    }

    /**
     * Load articlesLists from xml file.
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
     * Check if playlists have current theme lists definitions.
     *
     * @param Theme $theme
     * @param array $themePlaylists
     *
     * @return bool
     */
    public function checkIfThemePlaylistsAreUpToDate($theme, $themePlaylists)
    {
        if (empty($themePlaylists)) {
            return false;
        }

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

            foreach ($themePlaylist['templates'] as $template) {
                if (is_array($themes[$theme->getId()])) {
                    if (!in_array($template, $themes[$theme->getId()])) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Update playists with current theme playlists definitions.
     *
     * @param Theme $theme
     * @param array $themePlaylists
     *
     * @return bool
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
     * Clear playlists themes definitions on unassign action.
     *
     * @param Theme $theme
     * @param array $themePlaylists
     *
     * @return bool
     */
    public function removeThemeFromPlaylists($theme, $themePlaylists)
    {
        if (empty($themePlaylists)) {
            return false;
        }

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
     * Clear cache for all temlates assigned to playlist.
     *
     * @param Playlist $playlist
     */
    public function clearPlaylistTemplates($playlist)
    {
        foreach ($playlist->getThemes() as $theme) {
            if (is_array($theme)) {
                foreach ($theme as $file) {
                    \TemplateCacheHandler_DB::clean($file);
                }
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

        foreach ($themePlaylists['list'] as $themePlaylist) {
            $newThemePlaylists[$themePlaylist['@attributes']['name']] = array();
            if (array_key_exists('@attributes', $themePlaylist['template'])) {
                $bakThemePlaylist = $themePlaylist;
                $themePlaylist = array();
                $themePlaylist['template'][0] = $bakThemePlaylist['template'];
            }

            foreach ($themePlaylist['template'] as $template) {
                $newThemePlaylists[$themePlaylist['@attributes']['name']]['templates'][] = $template['@attributes']['file'];
            }
        }

        return $newThemePlaylists;
    }
}
