<?php
/**
 * @package Newscoop\Gimme
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
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
use Newscoop\Entity\Snippet\SnippetTemplate;
use Newscoop\Entity\Snippet\SnippetTemplate\SnippetTemplateField;

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
     *     },
     *     parameters={
     *         {"name"="show", "dataType"="string", "required"=false, "description"="Define which snippets to show, 'enabled', 'disabled', 'all'. Defaults to 'enabled'"}
     *     },
     * )
     *
     * @Route("/snippets.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getSnippetsAction(Request $request)
    {
        $show = $request->query->get('show', 'enabled');
        $em = $this->container->get('em');

        $snippets = $em->getRepository('Newscoop\Entity\Snippet')
            ->getSnippets($show);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $snippets = $paginator->paginate($snippets, array(
            'distinct' => false
        ));

        return $snippets;
    }

    /**
     * Get Snippet
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the Snippet is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="Snippet id"},
     *         {"name"="show", "dataType"="string", "required"=false, "description"="Define which snippets to show, 'enabled', 'disabled', 'all'. Defaults to 'enabled'"}
     *     },
     *     output="\Newscoop\Entity\Snippet"
     * )
     *
     * @Route("/snippets/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     *
     * @return array
     */
    public function getSnippetAction(Request $request, $id)
    {
        $show = $request->query->get('show', 'enabled');
        $em = $this->container->get('em');

        $snippetRepo = $em->getRepository('Newscoop\Entity\Snippet');
        $snippet = $snippetRepo->getSnippetById($id, $show);

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
     *         {"name"="language", "dataType"="string", "required"=true, "description"="Language code"},
     *         {"name"="show", "dataType"="string", "required"=false, "description"="Define which snippets to show, 'enabled', 'disabled', 'all'. Defaults to 'enabled'"}
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
        $show = $request->query->get('show', 'enabled');
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
            ->getArticleSnippets($number, $language, $show)
            ->getResult();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articleSnippets = $paginator->paginate($articleSnippets);

        return $articleSnippets;
    }

    /**
     * Create new Snippet
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when comment created succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\SnippetType"
     * )
     *
     * @Route("/snippets.{_format}", defaults={"_format"="json"})
     * @Route("/snippets/article/{articleNumber}/{languageCode}.{_format}", defaults={"_format"="json"})
     * @Method("POST")
     * @View()
     *
     * @return Form
     */
    public function createSnippetAction(Request $request)
    {
        return $this->processForm($request);
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
     * @Route("/snippets/{snippetId}.{_format}", defaults={"_format"="json"})
     * @Route("/snippets/article/{article}/{language}/{snippetId}.{_format}", defaults={"_format"="json"})
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
     * Delete Snippet
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when Snippet removed succesfuly",
     *         404={
     *           "Returned when the Snippet is not found",
     *         },
     *         409="Returned when Snippet is used by Articles"
     *     },
     *     parameters={
     *         {"name"="force", "dataType"="boolean", "required"=false, "description"="Force delete"},
     *     }
     * )
     *
     * @Route("/snippets/{snippetId}.{_format}", defaults={"_format"="json"})
     * @Route("/snippets/article/{articleNumber}/{languageCode}/{snippetId}.{_format}", defaults={"_format"="json"})
     * @Method("DELETE")
     * @View(statusCode=204)
     */
    public function deleteSnippetAction(Request $request, $snippetId, $articleNumber = null, $languageCode = null)
    {
        $force = $request->query->get('force', false);
        $em = $this->container->get('em');
        $articleSnippets = $em->getRepository('Newscoop\Entity\Snippet')
            ->deleteSnippet($snippetId, $force);
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
    private function processForm($request, $snippetId = null, $articleNumber = null, $languageCode = null)
    {
        $em = $this->container->get('em');

        if (!$snippetId) {
            $templateId = $request->request->get('template');

            if (!is_numeric($templateId)) {
                throw new InvalidArgumentException("Parameter 'template' is not numeric");
            }

            $snippetTemplate = $em->getRepository('Newscoop\Entity\Snippet\SnippetTemplate')
                ->getTemplateById($templateId);
            
            if (is_null($snippetTemplate)) {
                $snippetTemplate = $em->getRepository('Newscoop\Entity\Snippet\SnippetTemplate')
                    ->getTemplateById($templateId, 'all');
                
                if (is_null($snippetTemplate)) {
                    throw new InvalidArgumentException("Template with ID: '".$templateId."' does not exist.");
                }

                throw new InvalidArgumentException("Template with ID: '".$templateId."' is not enabled.");
            }

            $snippet = new Snippet($snippetTemplate);
            $statusCode = 201;
        } else {
            $snippet = $em->getRepository('Newscoop\Entity\Snippet')
                ->getSnippetById($snippetId);
            $statusCode = 200;
        }

        $form = $this->container->get('form.factory')->create(new SnippetType(), $snippet);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $snippet = $form->getData();
            $em->getRepository('Newscoop\Entity\Snippet')
                ->save($snippet);
            $response = new Response();
            $response->setStatusCode($statusCode);

            $response->headers->set(
                'X-Location',
                $this->generateUrl('newscoop_gimme_snippets_getsnippet', array(
                    'id' => $snippet->getId(),
                ), true)
            );

            return $response;
        }

        return $form;
    }
}
