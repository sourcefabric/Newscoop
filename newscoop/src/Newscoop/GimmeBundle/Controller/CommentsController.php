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
use Newscoop\GimmeBundle\Node\NodeTree;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Newscoop\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\ORM\EntityNotFoundException;
use Newscoop\GimmeBundle\Form\Type\CommentType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Newscoop\Entity\Comment;

class CommentsController extends FOSRestController
{
    /**
     * Get all comments
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the comments are not found"
     *         }
     *     }
     * )
     *
     * @Route("/comments.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getCommentsAction()
    {
        $em = $this->container->get('em');

        $showHidden = false;
        try {
            $user = $this->container->get('user')->getCurrentUser();
            if ($user && $user->isAdmin()) {
                $showHidden = true;
            }
        } catch (AuthenticationException $e) {}

        $comments = $em->getRepository('Newscoop\Entity\Comment')
            ->getComments(false, $showHidden);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $comments = $paginator->paginate($comments, array(
            'distinct' => false
        ));

        return $comments;
    }

    /**
     * Get comment
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the comment is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="Comment id"}
     *     },
     *     output="\Newscoop\Entity\Comment"
     * )
     *
     * @Route("/comments/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     *
     * @return Form
     */
    public function getCommentAction($id)
    {
        $em = $this->container->get('em');

        $comment = $em->getRepository('Newscoop\Entity\Comment')
            ->getComment($id, false)
            ->getOneOrNullResult();

        if (!$comment) {
            throw new EntityNotFoundException('Result was not found.');
        }

        return $comment;
    }

    /**
     * Get comments for article
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when successful but article doesn't have comments.",
     *         404={
     *           "Returned when the comments are not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"},
     *         {"name"="language", "dataType"="string", "required"=true, "description"="Language code"},
     *         {"name"="order", "dataType"="string", "required"=false, "description"="Ordering type. Possible values: [chrono, nested]"}
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/comments/{order}.{_format}", defaults={"_format"="json", "order"="chrono"}, options={"expose"=true})
     * @Route("/comments/article/{number}/{language}/{order}.{_format}", defaults={"_format"="json", "order"="chrono"}, options={"expose"=true})
     * @Route("/comments/article/{number}/{language}/{order}/recommended.{_format}", defaults={"_format"="json", "order"="chrono"}, options={"expose"=true}, name="newscoop_gimme_comments_getcommentsforarticle_recommended")
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getCommentsForArticleAction(Request $request, $number, $language, $order)
    {
        $em = $this->container->get('em');
        $paginatorService = $this->get('newscoop.paginator.paginator_service');
        $paginatorService->setUsedRouteParams(array('number' => $number, 'language' => $language));

        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $language)
            ->getOneOrNullResult();

        if (!$article) {
            throw new NotFoundHttpException('Article with number:"'.$number.'" and language: "'.$language.'" was not found.');
        }

        $recommended = false;
        if ($request->attributes->get('_route') === 'newscoop_gimme_comments_getcommentsforarticle_recommended') {
            $recommended = true;
        }

        $showHidden = false;
        try {
            $user = $this->container->get('user')->getCurrentUser();
            if ($user && $user->isAdmin()) {
                $showHidden = true;
            }
        } catch (AuthenticationException $e) {}

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $sort = $paginator->getPagination()->getSort();
        $articleComments = $em->getRepository('Newscoop\Entity\Comment')
            ->getArticleComments($number, $language, $recommended, false, $showHidden, $sort)
            ->getResult();

        if ($order == 'nested' && $articleComments) {
            $nodeTree = new NodeTree();
            $nodeTree->build($articleComments);
            $articleComments = $nodeTree->getFlattened();
        }

        $articleComments = $paginator->paginate($articleComments);

        return $articleComments;
    }

    /**
     * Create new comment
     *
     * **Comment available statuses:**
     *
     *     APPROVED - 0
     *     PENDING  - 1
     *     HIDDEN   - 2
     *     DELETED  - 3
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when comment created succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\CommentType"
     * )
     *
     * @Route("/comments/article/{articleNumber}/{languageCode}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_comments_createcomment")
     * @Method("POST")
     * @View()
     *
     * @return Form
     */
    public function createCommentAction(Request $request, $articleNumber, $languageCode)
    {
        return $this->processForm($request, null, $articleNumber, $languageCode);
    }

    /**
     * Update comment
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when comment updated succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\CommentType"
     * )
     *
     * @Route("/comments/{commentId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/comments/article/{article}/{language}/{commentId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("POST|PATCH")
     * @View()
     *
     * @return Form
     */
    public function updateCommentAction(Request $request, $commentId)
    {
        return $this->processForm($request, $commentId);
    }

    /**
     * Delete comment
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when comment removed succesfuly",
     *         404={
     *           "Returned when the comment is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Image id"}
     *     }
     * )
     *
     * @Route("/comments/{commentId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/comments/article/{articleNumber}/{languageCode}/{commentId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("DELETE")
     * @View(statusCode=204)
     *
     * @return Form
     */
    public function deleteCommentAction(Request $request, $commentId, $articleNumber = null, $languageCode = null)
    {
        $commentService = $this->container->get('comment');
        $em = $this->container->get('em');
        $comment = $em->getRepository('Newscoop\Entity\Comment')
            ->getComment($commentId, false)
            ->getOneOrNullResult();

        if (!$comment) {
            throw new EntityNotFoundException('Result was not found.');
        }

        $commentService->remove($comment);
    }

    /**
     * Process comment form
     *
     * @param Request $request
     * @param integer $comment
     * @param integer $articleNumber
     * @param string  $languageCode
     *
     * @return Form
     */
    private function processForm($request, $comment = null, $articleNumber = null, $languageCode = null)
    {
        $publicationService = $this->get('newscoop.publication_service');
        $publication = $publicationService->getPublication();

        if (
            false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') &&
            false === (bool) $publication->getPublicCommentsEnabled()
        ) {
            throw new AccessDeniedException('Public comments are disabled');
        }

        $em = $this->container->get('em');
        $commentService = $this->container->get('comment');

        if (!$comment) {
            $comment = new Comment();
            $statusCode = 201;
            $patch = false;
        } else {
            $statusCode = 200;
            $comment = $em->getRepository('Newscoop\Entity\Comment')->findOneById($comment);

            if (!$comment) {
                throw new EntityNotFoundException('Result was not found.');
            }
            $patch = true;
        }

        $form = $this->createForm(new CommentType(array('patch'=>$patch)), array(), array('method'=>$request->getMethod()));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $attributes = $form->getData();
            $user = $this->getUser();

            if ($comment->getId() !== null) {
                // update comment
                $comment = $commentService->updateComment($comment, $attributes);
            } else {
                // create new comment
                if ($user) {
                    $attributes['user'] = $user->getId();
                } else if (!$attributes['name']) {
                    throw new InvalidArgumentException('When user is not logged in, then commenter name is required.');
                }

                if ($articleNumber) {
                    $attributes['thread'] = $articleNumber;
                }

                if ($languageCode) {
                    $attributes['language'] = $languageCode;
                }

                $attributes['time_created'] = new \DateTime();
                $attributes['ip'] = $request->getClientIp();

                $comment = $commentService->save($comment, $attributes, $user);
            }

            $response = new Response();
            $response->setStatusCode($statusCode);

            $response->headers->set(
                'X-Location',
                $this->generateUrl('newscoop_gimme_comments_getcomment', array(
                    'id' => $comment->getId(),
                ), true)
            );

            return $response;
        }

        return $form;
    }
}
