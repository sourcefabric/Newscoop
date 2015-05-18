<?php

/**
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
use FOS\RestBundle\View as FOSView;
use Newscoop\GimmeBundle\Form\Type\PlaylistType;
use Newscoop\Entity\Playlist;
use Newscoop\Entity\Article;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Newscoop\Exception\ResourcesConflictException;
use Newscoop\Exception\InvalidParametersException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Newscoop\Exception\AuthenticationException;
use Newscoop\Criteria\ArticleSearchCriteria;

class ArticlesListController extends FOSRestController
{
    /**
     * Get Articles Lists.
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
     * @Route("/articles-lists.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     *
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
            'distinct' => false,
        ));

        return $playlists;
    }

    /**
     * Get Articles List.
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when articles lists found",
     *         404={
     *           "Returned when articles lists are not found",
     *         }
     *     },
     *     output="\Newscoop\Entity\Playlist"
     * )
     *
     * @Route("/articles-lists/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_lists_getlist")
     *
     * @Method("GET")
     * @View(serializerGroups={"details"})
     */
    public function getArticlesListAction(Request $request, $id)
    {
        $em = $this->container->get('em');

        $playlist = $em->getRepository('Newscoop\Entity\Playlist')
            ->getPlaylist($id)
            ->getOneOrNullResult();

        if (!$playlist) {
            throw new NotFoundHttpException('Result was not found.');
        }

        return $playlist;
    }

    /**
     * Get list of articles from "playlist".
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
     * @Route("/articles-lists/{id}/articles.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     *
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getPlaylistsArticlesAction(Request $request, $id)
    {
        $em = $this->container->get('em');

        $paginatorService = $this->get('newscoop.paginator.paginator_service');
        $paginatorService->setUsedRouteParams(array('id' => $id));

        $playlist = $em->getRepository('Newscoop\Entity\Playlist')
            ->findOneBy(array(
                'id' => $id,
            ));

        if (!$playlist) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $onlyPublished = true;
        try {
            $user = $this->container->get('user')->getCurrentUser();
            if ($user && $user->isAdmin()) {
                $onlyPublished = false;
            }
        } catch (AuthenticationException $e) {
        }

        $playlistArticles = $em->getRepository('Newscoop\Entity\Playlist')
            ->articles($playlist, array(), true, null, null, $onlyPublished, true)->getResult();

        $articlesIds = array();
        foreach ($playlistArticles as $playlistArticle) {
            $articles[] = $playlistArticle->getArticle();
        }

        /*$articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticlesByCriteria(new ArticleSearchCriteria(), $articlesIds, $onlyPublished, false);
*/
        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articles = $paginator->paginate($articles, array(
            'distinct' => false,
        ));

        $allItems = array_merge(array(
            'id' => $playlist->getId(),
            'title' => $playlist->getName(),
            'notes' => $playlist->getNotes(),
            'maxItems' => $playlist->getMaxItems(),
            'articlesModificationTime' => $playlist->getArticlesModificationTime(),
        ), $articles);

        return $allItems;
    }

    /**
     * Link article to playlist.
     *
     * ** articles headers**:
     *
     *     header name: "link"
     *     header value: "</api/articles/1; rel="article">"
     * or with specific language
     *
     *     header value: "</api/articles/1/en; rel="article">"
     * you can also specify position on list
     *
     *     header value: "</api/articles/1/en; rel="article">,<1; rel="article-position">"
     *
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when resource not found"
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="Playlist Id"}
     *     }
     * )
     *
     * @Route("articles-lists/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_lists_linkarticle")
     *
     * @Method("LINK")
     * @View(statusCode=201)
     */
    public function linkToPlaylistAction(Request $request, $id)
    {
        $em = $this->container->get('em');
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('ManagePlaylist')) {
            throw new AccessDeniedException('You do not have the right to manage playlists.');
        }

        $playlist = $em->getRepository('Newscoop\Entity\Playlist')
            ->getPlaylist($id)
            ->getOneOrNullResult();

        if (!$playlist) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $this->linkOrUnlinkResources($playlist, $request, 'link');

        return $playlist;
    }

    /**
     * Unlink article to playlist.
     *
     * ** articles headers**:
     *
     *     header name: "link"
     *     header value: "</api/articles/1; rel="article">"
     * or with specific language
     *
     *     header value: "</api/articles/1/en; rel="article">"
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when resource not found"
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="Playlist Id"}
     *     }
     * )
     *
     * @Route("articles-lists/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_lists_unlinkarticle")
     *
     * @Method("UNLINK")
     * @View(statusCode=204)
     */
    public function unlinkFromPlaylistAction(Request $request, $id)
    {
        $em = $this->container->get('em');
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('ManagePlaylist')) {
            throw new AccessDeniedException('You do not have the right to manage playlists.');
        }

        $playlist = $em->getRepository('Newscoop\Entity\Playlist')
            ->getPlaylist($id)
            ->getOneOrNullResult();

        if (!$playlist) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $this->linkOrUnlinkResources($playlist, $request, 'unlink');

        return $playlist;
    }

    /**
     * Save many changes for playlist items.
     *
     * example post data:
     *```
     * 'actions' => array [
     *   0 => array [
     *     "link" => "</api/articles/67/en; rel="article">,<3; rel="article-position">"
     *   ]
     *   1 => array [
     *     "unlink" => "</api/articles/64/en; rel="article">"
     *   ]
     *   2 => array [
     *     "link" => "</api/articles/64/en; rel="article">,<1; rel="article-position">"
     *   ]
     * ]
     *```
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when resource not found"
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="Playlist Id"}
     *     },
     *     parameters={
     *         {"name"="articlesModificationTime", "dataType"="datetime", "required"=true, "description"="Playlist articles list last modification time"}
     *     }
     * )
     *
     * @Route("articles-lists/{id}/articles.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     *
     * @Method("POST")
     * @View(statusCode=200)
     */
    public function saveBatchActionsAction(Request $request, $id)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('ManagePlaylist')) {
            throw new AccessDeniedException('You do not have the right to manage playlists.');
        }

        $em = $this->container->get('em');
        $urlMatcher = $this->container->get('router');
        $controllerResolver = $this->container->get('controller_resolver');
        $kernel = $this->container->get('kernel');
        $dispatcher = $this->container->get('dispatcher');

        $playlist = $em->getRepository('Newscoop\Entity\Playlist')
            ->getPlaylist($id)
            ->getOneOrNullResult();

        if (!$playlist) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $actions = $request->request->get('actions', array());
        $lastArtclesModificationTime = $request->request->get('articlesModificationTime');
        if (!$lastArtclesModificationTime && $playlist->getArticlesModificationTime() != null) {
            throw new InvalidParametersException('articlesModificationTime parameter is required');
        } elseif (new \DateTime($lastArtclesModificationTime, new \DateTimeZone('UTC')) !=
            $playlist->getArticlesModificationTime() &&
            $playlist->getArticlesModificationTime() != null
        ) {
            throw new ResourcesConflictException('This list is already in a different state than the one in which it was loaded.', 409);
        }

        $playlist->setArticlesModificationTime(new \DateTime('now'));
        $em->flush();

        // The controller resolver needs a request to resolve the controller.
        $stubRequest = new Request();
        $actionsResults = array();
        foreach ($actions as $actionKey => $action) {
            foreach ($action as $method => $header) {
                $urlMatcher->getContext()->setMethod($method);
                switch ($method) {
                    case 'link':
                        $resource = $this->generateUrl('newscoop_gimme_articles_lists_linkarticle', array('id' => $playlist->getId()));
                        break;

                    case 'unlink':
                        $resource = $this->generateUrl('newscoop_gimme_articles_lists_unlinkarticle', array('id' => $playlist->getId()));
                        break;
                }

                $tempRequest = Request::create($resource);
                try {
                    $route = $urlMatcher->match($tempRequest->getRequestUri());
                } catch (\Exception $e) {
                    // If we don't have a matching route we return the original Link header
                    continue;
                }

                $stubRequest->attributes->replace($route);
                $stubRequest->server = $request->server;
                $stubRequest->headers->set('link', $header);
                if (false === $controller = $controllerResolver->getController($stubRequest)) {
                    continue;
                }

                $subEvent = new FilterControllerEvent($kernel, $controller, $stubRequest, HttpKernelInterface::SUB_REQUEST);
                $kernelSubEvent = new GetResponseEvent($kernel, $stubRequest, HttpKernelInterface::SUB_REQUEST);
                $dispatcher->dispatch(KernelEvents::REQUEST, $kernelSubEvent);
                $dispatcher->dispatch(KernelEvents::CONTROLLER, $subEvent);
                $controller = $subEvent->getController();

                $arguments = $controllerResolver->getArguments($stubRequest, $controller);

                try {
                    $result = call_user_func_array($controller, $arguments);

                    if (!is_object($result)) {
                        continue;
                    }
                    $actionsResults[$actionKey] = array(
                        'object' => $result,
                        'method' => $method,
                        'header' => $header,
                    );
                } catch (\Exception $e) {
                    $actionsResults[$actionKey] = array(
                        'object' => $e->getMessage(),
                        'method' => $method,
                        'header' => $header,
                    );

                    continue;
                }
            }
        }

        return $actionsResults;
    }

    /**
     * Create new playlist.
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when resource not found"
     *     },
     *     parameters={
     *         {"name"="access_token", "dataType"="string", "required"=false, "description"="Access token"}
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\PlaylistType"
     * )
     *
     * @Route("articles-lists.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     *
     * @Method("POST")
     */
    public function createPlaylistAction(Request $request)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('ManagePlaylist')) {
            throw new AccessDeniedException('You do not have the right to manage playlists.');
        }

        $em = $this->container->get('em');
        $playlist = new Playlist();

        $form = $this->createForm(new PlaylistType(), $playlist);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $existingPlaylist = $em->getRepository('Newscoop\Entity\Playlist')
                ->getPlaylistByTitle($playlist->getName())
                ->getOneOrNullResult();

            if ($existingPlaylist) {
                throw new ResourcesConflictException('Playlist with that name already exists', 409);
            }

            $em->persist($playlist);
            $em->flush();

            $view = FOSView\View::create($playlist, 200);
            $view->setHeader('X-Location', $this->generateUrl('newscoop_gimme_articles_lists_getlist', array(
                'id' => $playlist->getId(),
            ), true));

            return $view;
        }

        return $form;
    }

    /**
     * Update playlist.
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when resource not found"
     *     },
     *     parameters={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="Playlist id"}
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\PlaylistType"
     * )
     *
     * @Route("articles-lists/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     *
     * @Method("PATCH|POST")
     */
    public function updatePlaylistAction(Request $request, $id)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('ManagePlaylist')) {
            throw new AccessDeniedException('You do not have the right to manage playlists.');
        }

        $em = $this->container->get('em');
        $playlist = $em->getRepository('Newscoop\Entity\Playlist')
            ->getPlaylist($id)
            ->getOneOrNullResult();

        if (!$playlist) {
            throw new NotFoundHttpException('Result was not found.');
        }
        $oldMaxItems = $playlist->getMaxItems();

        $form = $this->createForm(new PlaylistType(), $playlist, array(
            'method' => $request->getMethod(),
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->persist($playlist);
            $em->flush();

            if ($oldMaxItems != $playlist->getMaxItems()) {
                $playlistService = $this->get('playlists');
                $playlistService->removeLeftItems($playlist);
                $playlist = $em->getRepository('Newscoop\Entity\Playlist')
                    ->getPlaylist($playlist->getId())
                    ->getOneOrNullResult();
            }

            $view = FOSView\View::create($playlist, 201);
            $view->setHeader('X-Location', $this->generateUrl('newscoop_gimme_articles_lists_getlist', array(
                'id' => $playlist->getId(),
            ), true));

            return $view;
        }

        return $form;
    }

    /**
     * Delete playlist.
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when playlist removed succesfuly",
     *         404={
     *           "Returned when the playlist is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="{laylist id"}
     *     }
     * )
     *
     * @Route("articles-lists/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     *
     * @Method("DELETE")
     * @View(statusCode=204)
     *
     * @return Form
     */
    public function deletePlaylistAction(Request $request, $id)
    {
        $user = $this->container->get('user')->getCurrentUser();
        if (!$user->hasPermission('ManagePlaylist')) {
            throw new AccessDeniedException('You do not have the right to manage playlists.');
        }

        $em = $this->container->get('em');
        $playlist = $em->getRepository('Newscoop\Entity\Playlist')
            ->getPlaylist($id)
            ->getOneOrNullResult();

        if (!$playlist) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $em->remove($playlist);
        $em->flush();
    }

    private function linkOrUnlinkResources($playlist, $request, $action = 'link')
    {
        $matched = false;
        $resources = $request->attributes->get('links', array());
        foreach ($resources as $key => $objectArray) {
            if (!is_array($objectArray)) {
                return true;
            }

            $object = $objectArray['object'];
            if ($object instanceof \Exception) {
                throw $object;
            }

            if ($object instanceof Article) {
                if ($action == 'link') {
                    $position = false;
                    if (count($notConvertedLinks = $this->getNotConvertedLinks($request)) > 0) {
                        foreach ($notConvertedLinks as $link) {
                            if (isset($link['resourceType']) && $link['resourceType'] == 'article-position') {
                                $position = $link['resource'];
                            }
                        }
                    }
                    $playlistService = $this->get('playlists');
                    $playlistService->addArticle($playlist, $object, $position);
                } elseif ($action == 'unlink') {
                    $playlistService = $this->get('playlists');
                    $playlistService->removePlaylistArticle($playlist, $object);
                }

                $matched = true;

                continue;
            }
        }

        if ($matched === false) {
            throw new InvalidParametersException('Any supported link object not found');
        }
    }

    private function getNotConvertedLinks($request)
    {
        $links = array();
        foreach ($request->attributes->get('links') as $idx => $link) {
            if (is_string($link)) {
                $linkParams = explode(';', trim($link));
                $resourceType = null;
                if (count($linkParams) > 1) {
                    $resourceType = trim(preg_replace('/<|>/', '', $linkParams[1]));
                    $resourceType = str_replace('"', '', str_replace('rel=', '', $resourceType));
                }
                $resource = array_shift($linkParams);
                $resource = preg_replace('/<|>/', '', $resource);

                $links[] = array(
                    'resource' => $resource,
                    'resourceType' => $resourceType,
                );
            }
        }

        return $links;
    }
}
