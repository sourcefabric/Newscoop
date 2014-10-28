<?php

namespace Newscoop\ArticlesBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\ArticlesBundle\Form\Type\EditorialCommentType;
use Newscoop\ArticlesBundle\Entity\EditorialComment;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

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
     * @Route("/articles/{number}/{language}/editorial_comments.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_get_editorial_comment")
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getCommentsAction(Request $request)
    {
        $em = $this->container->get('em');

        $editorialComments = $em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')->getAll();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $editorialComments = $paginator->paginate($editorialComments, array(
            'distinct' => false
        ));

        return $editorialComments;
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
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/editorial_comments.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_create_editorial_comment")
     * @Method("POST")
     * @View(serializerGroups={"list"})
     */
    public function createCommentAction(Request $request, $number, $language)
    {
        $em = $this->container->get('em');
        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $language)
            ->getOneOrNullResult();

        return $this->processForm($request, $article);
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
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/editorial_comments/{commentId}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_edit_editorial_comment")
     * @Method("POST")
     * @View(serializerGroups={"list"})
     */
    public function editCommentAction(Request $request, $number, $language, $commentId)
    {
        return $this->processForm($request, null, $commentId);
    }

    public function removeCommentAction(Request $request, $number, $language, $commentId)
    {
        //return $this->processForm($request, null, $commentId);
    }

    /**
     * Process editorial comments form
     *
     * @param Request $request
     *
     * @return Form
     */
    private function processForm($request, $article = null, $comment = null)
    {
        $em = $this->container->get('em');
        $editorialCommentService = $this->container->get('newscoop.editorial_comments');

        if (!$comment) {
            $statusCode = 201;
        } else {
            $statusCode = 200;
            $comment = $em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')->findOneBy(array('id' => 1));

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

            if ($statusCode == 201 && $article) {
                $comment = $editorialCommentService->create($attributes['comment'], $article, $user);

                // TODO: apply here new route
    /*            $response->headers->set(
                    'X-Location',
                    $this->generateUrl('newscoop_gimme_attachments_getattachment', array(
                        'number' => $comment->getId(),
                    ), true)
                );*/
            } elseif ($statusCode == 200 && $comment) {
                $comment = $editorialCommentService->edit($attributes['comment'], $comment, $user);
            }

            return $response;
        }

        return $form;
    }
}
