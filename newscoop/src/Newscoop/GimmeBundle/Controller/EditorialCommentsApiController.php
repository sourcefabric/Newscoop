<?php

namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\ArticlesBundle\Form\Type\EditorialCommentType;
use Newscoop\ArticlesBundle\Entity\EditorialComment;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityNotFoundException;

class EditorialCommentsApiController extends FOSRestController
{
    /**
     * Get editorial comments
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when success",
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/editorial_comments/order/{order}.{_format}", defaults={"_format"="json", "order"="chrono"}, options={"expose"=true}, name="newscoop_gimme_articles_get_editorial_comments")
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getCommentsAction(Request $request, $number, $language, $order)
    {
        $em = $this->container->get('em');

        $editorialComments = $em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')
            ->getAllByArticleNumber($number)->getResult();

        if ($order == 'nested' && $editorialComments) {
            $root = new \Node(0,0,'');
            $reSortedComments = array();
            foreach ($editorialComments as $comment) {
                $reSortedComments[$comment->getId()] = $comment;
            }

            ksort($reSortedComments);

            foreach ($reSortedComments as $comment) {
                if ($comment->getParent() instanceof EditorialComment) {
                    $node = new \Node($comment->getId(), $comment->getParent()->getId(), $comment);
                } else {
                    $node = new \Node($comment->getId(), 0, $comment);
                }
                $root->insertNode($node);
            }

            $editorialComments = $root->flatten(false);
        }

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $paginator->setUsedRouteParams(array('number' => $number, 'language' => $language));
        $editorialComments = $paginator->paginate($editorialComments);

        return $editorialComments;
    }

    /**
     * Get single editorial comment
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when success",
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/editorial_comments/{commentId}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_get_editorial_comment")
     * @Method("GET")
     * @View(serializerGroups={"details"})
     */
    public function getCommentAction(Request $request, $number, $language, $commentId)
    {
        $em = $this->container->get('em');

        $comment = $em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')
            ->getOneByArticleAndCommentId($number, $language, $commentId)
            ->getOneOrNullResult();

        if (!$comment) {
            throw new EntityNotFoundException('Result was not found.');
        }

        return $comment;
    }

    /**
     * Create editorial comments
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when editorial comment is created",
     *         404={
     *           "Returned when article is not found",
     *         }
     *     },
     *     input="\Newscoop\ArticlesBundle\Form\Type\EditorialCommentType"
     * )
     *
     * @Route("/articles/{number}/{language}/editorial_comments.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_create_editorial_comment")
     * @Method("POST")
     * @View(serializerGroups={"list"})
     */
    public function createCommentAction(Request $request, $number, $language)
    {
        return $this->processForm($request, $number, $language);
    }

    /**
     * Edit editorial comments
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when editorial comment is created",
     *         404={
     *           "Returned when article is not found",
     *         }
     *     },
     *     input="\Newscoop\ArticlesBundle\Form\Type\EditorialCommentType"
     * )
     *
     * @Route("/articles/{number}/{language}/editorial_comments/{commentId}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_edit_editorial_comment")
     * @Method("POST")
     * @View(serializerGroups={"list"})
     */
    public function editCommentAction(Request $request, $number, $language, $commentId)
    {
        return $this->processForm($request, $number, $language, $commentId);
    }

    /**
     * Edit editorial comments
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when editorial comment is removed",
     *         404={
     *           "Returned when entity is not found",
     *         }
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/editorial_comments/{commentId}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_remove_editorial_comment")
     * @Method("DELETE")
     * @View(serializerGroups={"list"})
     */
    public function removeCommentAction(Request $request, $number, $language, $commentId)
    {
        $em = $this->container->get('em');
        $editorialCommentService = $this->container->get('newscoop.editorial_comments');
        $user = $this->container->get('user')->getCurrentUser();

        $comment = $em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')
            ->getOneByArticleAndCommentId($number, $language, $commentId)
            ->getOneOrNullResult();

        if (!$comment) {
            throw new EntityNotFoundException('Result was not found.');
        }

        $editorialCommentService->remove($comment, $user);

        $response = new Response();
        $response->setStatusCode(204);

        return $response;
    }

    /**
     * Process editorial comments form
     *
     * @param Request $request
     *
     * @return Form
     */
    private function processForm($request, $articleNumber = null, $languageId = null, $commentId = null)
    {
        $em = $this->container->get('em');
        $editorialCommentService = $this->container->get('newscoop.editorial_comments');

        if (!$commentId) {
            $statusCode = 201;
        } else {
            $statusCode = 200;
            $comment = $em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')
                ->getOneByArticleAndCommentId($articleNumber, $languageId, $commentId)
                ->getOneOrNullResult();

            if (!$comment) {
                throw new EntityNotFoundException('Result was not found.');
            }
        }

        $form = $this->createForm(new EditorialCommentType(), array());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $attributes = $form->getData();
            $user = $this->container->get('user')->getCurrentUser();

            $response = new Response();
            $response->setStatusCode($statusCode);

            if ($statusCode == 201 && $articleNumber && $languageId) {
                $article = $em->getRepository('Newscoop\Entity\Article')
                    ->getArticle($articleNumber, $languageId)
                    ->getOneOrNullResult();

                $parent = false;
                if (array_key_exists('parent', $attributes) && $attributes['parent'] != null) {
                    $parent = $em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')
                        ->getOneByArticleAndCommentId($articleNumber, $languageId, $attributes['parent'])
                        ->getOneOrNullResult();

                    if (!$parent) {
                        throw new EntityNotFoundException('Parent comment was not found.');
                    }
                }

                $comment = $editorialCommentService->create($attributes['comment'], $article, $user, $parent);

                $response->headers->set(
                    'X-Location',
                    $this->generateUrl('newscoop_gimme_articles_get_editorial_comment', array(
                        'number' => $articleNumber,
                        'language' => $languageId,
                        'commentId' => $comment->getId()
                    ), true)
                );
            } elseif ($statusCode == 200 && $comment) {
                if (array_key_exists('comment', $attributes) && $attributes['comment'] != $comment->getComment() && $attributes['comment'] != '') {
                    $editorialCommentService->edit($attributes['comment'], $comment, $user);
                }

                if (array_key_exists('resolved', $attributes) && $attributes['resolved'] != $comment->getResolved()) {
                    $editorialCommentService->resolve($comment, $user, $attributes['resolved']);
                }
            }

            return $response;
        }

        return $form;
    }
}
