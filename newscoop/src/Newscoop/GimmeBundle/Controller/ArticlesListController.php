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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticlesListController extends FOSRestController
{
    /**
     * @Route("/articles-lists.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getArticlesListAction(Request $request)
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
     * @Route("/articles-lists/{id}/articles.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getPlaylistsArticlesAction(Request $request, $id)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop.publication_service')->getPublication()->getId();

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