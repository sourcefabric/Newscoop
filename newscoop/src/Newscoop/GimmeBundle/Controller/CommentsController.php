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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\ORM\EntityNotFoundException;

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
     * @Route("/comments.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getCommentsAction()
    {
        $em = $this->container->get('em');

        $comments = $em->getRepository('Newscoop\Entity\Comment')
            ->getComments();

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
     * @Route("/comments/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     *
     * @return Form
     */
    public function getCommentAction($id)
    {
        $em = $this->container->get('em');

        $comment = $em->getRepository('Newscoop\Entity\Comment')
            ->getComment($id)
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
     *         404={
     *           "Returned when the comments are not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"},
     *         {"name"="language", "dataType"="string", "required"=true, "description"="Language code"}
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/comments.{_format}", defaults={"_format"="json"})
     * @Route("/comments/article/{number}/{language}.{_format}", defaults={"_format"="json"})
     * @Route("/comments/article/{number}/{language}/recommended.{_format}", defaults={"_format"="json"}, name="newscoop_gimme_comments_getcommentsforarticle_recommended")
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getCommentsForArticleAction(Request $request, $number, $language)
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

        $articleComments = $em->getRepository('Newscoop\Entity\Comment')
            ->getArticleComments($number, $language, $recommended);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articleComments = $paginator->paginate($articleComments);

        return $articleComments;
    }
}
