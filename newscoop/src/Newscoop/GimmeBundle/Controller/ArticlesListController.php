<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticlesListController extends FOSRestController
{
    /**
     * Get Articles Lists
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when articles lists found",
     *         404={
     *           "Returned when articles lists are not found",
     *         }
     *     }
     * )
     *
     * @Route("/articles-lists.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getArticlesListsAction(Request $request)
    {
        $em = $this->container->get('em');

        $playlists = $em->getRepository('Newscoop\Entity\Playlist')
            ->getPlaylists();

        if (!$playlists) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $playlists = $paginator->paginate($playlists, array(
            'distinct' => false
        ));

        return $playlists;
    }

    /**
     * Get list of articles from "playlist"
     *
     * Returns array with articles under "items" key and requested list "id" and "title"
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when articles found",
     *         404={
     *           "Returned when articles are not found",
     *         }
     *     }
     * )
     *
     * @Route("/articles-lists/{id}/articles.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getPlaylistsArticlesAction(Request $request, $id)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop_newscoop.publication_service')->getPublication()->getId();

        $paginatorService = $this->get('newscoop.paginator.paginator_service');
        $paginatorService->setUsedRouteParams(array('id' => $id));

        $playlist = $em->getRepository('Newscoop\Entity\Playlist')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$playlist) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticlesForPlaylist($publication, $id);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articles = $paginator->paginate($articles, array(
            'distinct' => false
        ));

        $allItems = array_merge(array(
            'id' => $playlist->getId(),
            'title' => $playlist->getName(),
        ), $articles);

        return $allItems;
    }
}
