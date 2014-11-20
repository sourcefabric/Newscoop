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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\ORM\EntityNotFoundException;
use Newscoop\GimmeBundle\Form\Type\SnippetType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Newscoop\Entity\Snippet;
use Newscoop\Entity\Snippet\SnippetTemplate;

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
     *         {"name"="show", "dataType"="string", "required"=false, "description"="Define which snippets to show, 'enabled', 'disabled', 'all'. Defaults to 'enabled'"},
     *         {"name"="rendered", "dataType"="string", "required"=false, "description"="Return a Rendered Snippet"}
     *     },
     * )
     *
     * @Route("/snippets.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getSnippetsAction(Request $request)
    {
        $show = $request->query->get('show', 'enabled');
        $rendered = $request->query->get('rendered', 'false');
        $em = $this->container->get('em');

        $snippets = $em->getRepository('Newscoop\Entity\Snippet')
            ->getSnippets($show);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $snippets = $paginator->paginate($snippets, array(
            'distinct' => false
        ));

        if ($view = $request->attributes->get('_view')) {
            if ($rendered == 'true') {
                $view->setSerializerGroups(array('rendered'));
            }
        }

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
     *         {"name"="show", "dataType"="string", "required"=false, "description"="Define which snippets to show, 'enabled', 'disabled', 'all'. Defaults to 'enabled'"},
     *         {"name"="rendered", "dataType"="string", "required"=false, "description"="Return a Rendered Snippet"},
     *     },
     *     output="\Newscoop\Entity\Snippet"
     * )
     *
     * @Route("/snippets/{snippetId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/articles/{articleNumber}/{languageCode}/snippets/{snippetId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"details", "scopeNoBackend"})
     *
     * @return array
     */
    public function getSnippetAction(Request $request, $snippetId)
    {
        // XXX Check if the SnippetID belongs to the articleNumber
        $show = $request->query->get('show', 'enabled');
        $rendered = $request->query->get('rendered', 'false');
        $em = $this->container->get('em');

        $snippetRepo = $em->getRepository('Newscoop\Entity\Snippet');
        $snippet = $snippetRepo->getSnippetById($snippetId, $show);

        if (!$snippet) {
            throw new EntityNotFoundException('Result was not found.');
        }

        if ($view = $request->attributes->get('_view')) {
            if ($rendered == 'true') {
                $view->setSerializerGroups(array('rendered'));
            }
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
     *         {"name"="show", "dataType"="string", "required"=false, "description"="Define which snippets to show, 'enabled', 'disabled', 'all'. Defaults to 'enabled'"},
     *         {"name"="rendered", "dataType"="string", "required"=false, "description"="Return a Rendered Snippet"}
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/snippets.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/snippets/article/{number}/{language}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getSnippetsForArticleAction(Request $request, $number, $language)
    {
        $show = $request->query->get('show', 'enabled');
        $rendered = $request->query->get('rendered', 'false');
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

        if ($view = $request->attributes->get('_view')) {
            if ($rendered == 'true') {
                $view->setSerializerGroups(array('rendered'));
            }
        }

        return $articleSnippets;
    }

    /**
     * Create new Snippet
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when Snippet created succesfuly"
     *     },
     *     parameters={
     *         {"name"="template", "dataType"="integer", "required"=true, "description"="SnippetTemplate ID"}
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\SnippetType"
     * )
     *
     * @Route("/snippets.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/articles/{articleNumber}/{languageCode}/snippets.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("POST")
     * @View()
     *
     * @return Form
     */
    public function createSnippetAction(Request $request, $articleNumber = null, $languageCode = null)
    {
        return $this->processForm($request, null, $articleNumber, $languageCode);
    }

    /**
     * Update Snippet
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when Snippet updated succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\CommentType"
     * )
     *
     * @Route("/snippets/{snippetId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/articles/{articleNumber}/{languageCode}/snippets/{snippetId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("POST|PATCH")
     * @View()
     *
     * @return Form
     */
    public function updateSnippetAction(Request $request, $snippetId, $articleNumber = null, $languageCode = null)
    {
        return $this->processForm($request, $snippetId, $articleNumber, $languageCode);
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
     * @Route("/snippets/{snippetId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/snippets/article/{articleNumber}/{languageCode}/{snippetId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
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
     * Process Snippet form
     *
     * @param Request $request
     * @param integer $snippetId
     * @param integer $articleNumber
     * @param string  $languageCode
     *
     * @return Form
     */
    private function processForm($request, $snippetId = null, $articleNumber = null, $languageCode = null)
    {
        // XXX It breaks using PATCH
        $em = $this->container->get('em');
        $patch = false;

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
                ->getSnippetById($snippetId, 'all');
            $statusCode = 200;
            $patch = true;
            if (is_null($snippet)) {
                throw new NotFoundHttpException("Snippet with ID: '".$snippetId."' was not found");
            }
        }

        $article = null;
        if (!is_null($articleNumber) && !is_null($languageCode)) {
            $article = $em->getRepository('Newscoop\Entity\Article')
                ->getArticle($articleNumber, $languageCode)
                ->getOneOrNullResult();

            if (!$article) {
                throw new NotFoundHttpException('Article with number:"'.$articleNumber.'" and language: "'.$languageCode.'" was not found.');
            }
        }

        $form = $this->container->get('form.factory')->create(new SnippetType(array('patch'=>$patch)), $snippet);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $snippet = $form->getData();
            if ($article) {
                $snippet->addArticle($article);
            }
            $em->getRepository('Newscoop\Entity\Snippet')
                ->save($snippet);
            $response = new Response();
            $response->setStatusCode($statusCode);

            $response->headers->set(
                'X-Location',
                $this->generateUrl('newscoop_gimme_snippets_getsnippet', array(
                    'snippetId' => $snippet->getId(),
                ), true)
            );

            return $response;
        }

        return $form;
    }
}