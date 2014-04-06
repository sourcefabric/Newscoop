<?php
/**
 * @package Newscoop\Gimme
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\ORM\EntityNotFoundException;
use Newscoop\GimmeBundle\Form\Type\SnippetType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Newscoop\Entity\Snippet;

class SnippetsController extends FOSRestController
{
    /**
     * Get all snippets
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the snippets are not found"
     *         }
     *     }
     * )
     *
     * @Route("/snippets.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getSnippetsAction()
    {
        $em = $this->container->get('em');

        $snippets = $em->getRepository('Newscoop\Entity\Snippet')
            ->findAll();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $snippets = $paginator->paginate($snippets, array(
            'distinct' => false
        ));

        return $snippets;
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
     *     output="\Newscoop\Entity\Snippet"
     * )
     *
     * @Route("/snippets/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     *
     * @return Form
     */
    public function getSnippetAction($id)
    {
        $em = $this->container->get('em');

        $snippetRepo = $em->getRepository('Newscoop\Entity\Snippet');
        $snippet     = $snippetRepo->find($id);

        if (!$snippet) {
            throw new EntityNotFoundException('Result was not found.');
        }

        return $snippet;
    }

    /**
     * Get snippets for article
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         204="Returned when successful but article doesn't have snippets.",
     *         404={
     *           "Returned when the snippets are not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Article number"},
     *         {"name"="language", "dataType"="string", "required"=true, "description"="Language code"}
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/snippets.{_format}", defaults={"_format"="json"})
     * @Route("/snippets/article/{number}/{language}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getSnippetsForArticleAction(Request $request, $number, $language)
    {
        $ladybug = \Zend_Registry::get('container')->getService('ladybug');
        $em = $this->container->get('em');
        $paginatorService = $this->get('newscoop.paginator.paginator_service');
        $paginatorService->setUsedRouteParams(array('number' => $number, 'language' => $language));

        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $language)
            ->getOneOrNullResult();

        if (!$article) {
            throw new NotFoundHttpException('Article with number:"'.$number.'" and language: "'.$language.'" was not found.');
        }

        $articleSnippets = $em->getRepository('Newscoop\Entity\Snippet')
            ->getArticleSnippets($number, $language)
            ->getResult();

        
        $ladybug->log($articleSnippets);
        

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articleSnippets = $paginator->paginate($articleSnippets);

        $ladybug->log($articleSnippets);

        return $articleSnippets;
    }

    /**
     * Create new comment
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when comment created succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\SnippetType"
     * )
     *
     * @Route("/snippets/article/{articleNumber}/{languageCode}.{_format}", defaults={"_format"="json"})
     * @Method("POST")
     * @View()
     *
     * @return Form
     */
    public function createSnippetAction(Request $request, $articleNumber, $languageCode)
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
     * @Route("/snippets/{commentId}.{_format}", defaults={"_format"="json"})
     * @Route("/snippets/article/{article}/{language}/{commentId}.{_format}", defaults={"_format"="json"})
     * @Method("POST|PATCH")
     * @View()
     *
     * @return Form
     */
    public function updateSnippetAction(Request $request, $snippetId)
    {
        return $this->processForm($request, $snippetId);
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
     * @Route("/snippets/{commentId}.{_format}", defaults={"_format"="json"})
     * @Route("/snippets/article/{articleNumber}/{languageCode}/{commentId}.{_format}", defaults={"_format"="json"})
     * @Method("DELETE")
     * @View(statusCode=204)
     *
     * @return Form
     */
    public function deleteSnippetAction(Request $request, $snippetId, $articleNumber = null, $languageCode = null)
    {
        $snippetService = $this->container->get('comment');
        $em = $this->container->get('em');
        $snippet = $em->getRepository('Newscoop\Entity\Snippet')
            ->getComment($snippetId, false)
            ->getOneOrNullResult();

        if (!$snippet) {
            throw new EntityNotFoundException('Result was not found.');
        }

        $snippetService->remove($snippet);
    }

    /**
     * Process comment form
     *
     * @param Request $request
     * @param integer $snippet
     * @param integer $articleNumber
     * @param string  $languageCode
     *
     * @return Form
     */
    private function processForm($request, $snippet = null, $articleNumber = null, $languageCode = null)
    {
        // $publicationService = $this->get('newscoop.publication_service');
        // $publication = $publicationService->getPublication();

        // if (
        //     false === $this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') &&
        //     false === (bool) $publication->getPublicSnippetsEnabled()
        // ) {
        //     throw new AccessDeniedException('Public snippets are disabled');
        // }

        // $em = $this->container->get('em');
        // $snippetService = $this->container->get('comment');

        // if (!$snippet) {
        //     $snippet = new Comment();
        //     $statusCode = 201;
        // } else {
        //     $statusCode = 200;
        //     $snippet = $em->getRepository('Newscoop\Entity\Snippet')->findOneById($snippet);

        //     if (!$snippet) {
        //         throw new EntityNotFoundException('Result was not found.');
        //     }
        // }

        $ladybug = \Zend_Registry::get('container')->getService('ladybug');
        $ladybug->log($request);

        $form = $this->createForm(new SnippetType(array('patch'=>true)), array());
        $form->handleRequest($request);

        
        $ladybug->log($form);
        $ladybug->log($form->getData());
        // print ladybug_dump($request);
        // print ladybug_dump($form);
        // print ladybug_dump($form->getData());

        // if ($form->isValid()) {
        //     $attributes = $form->getData();
        //     $user = $this->getUser();

        //     if ($snippet->getId() !== null) {
        //         // update comment
        //         $snippet = $snippetService->updateComment($snippet, $attributes);
        //     } else {



        //         // create new comment
        //         if ($user) {
        //             $attributes['user'] = $user->getId();
        //         } else if (!$attributes['name']) {
        //             throw new InvalidArgumentException('When user is not logged in, then commenter name is required.');
        //         }

        //         if ($articleNumber) {
        //             $attributes['thread'] = $articleNumber;
        //         }

        //         if ($languageCode) {
        //             $attributes['language'] = $languageCode;
        //         }

        //         $attributes['time_created'] = new \DateTime();
        //         $attributes['ip'] = $request->getClientIp();

        //         $snippet = $snippetService->save($snippet, $attributes, $user);
        //     }

        //     $response = new Response();
        //     $response->setStatusCode($statusCode);

        //     $response->headers->set(
        //         'X-Location',
        //         $this->generateUrl('newscoop_gimme_snippets_getcomment', array(
        //             'id' => $snippet->getId(),
        //         ), true)
        //     );

        //     return $response;
        // }

        return $form;
    }
}
