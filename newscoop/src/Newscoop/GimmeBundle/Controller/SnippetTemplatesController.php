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
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Doctrine\ORM\EntityNotFoundException;
use Newscoop\GimmeBundle\Form\Type\SnippetTemplateType;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Newscoop\Entity\Snippet;
use Newscoop\Entity\Snippet\SnippetTemplate;

class SnippetTemplatesController extends FOSRestController
{
    /**
     * Get all SnippetTemplates
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
     * @Route("/snippetTemplates.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getSnippetTemplatesAction(Request $request)
    {
        $show = $request->query->get('show', 'enabled');
        $em = $this->container->get('em');

        $snippetTemplates = $em->getRepository('Newscoop\Entity\Snippet\SnippetTemplate')
            ->getSnippetTemplateQueryBuilder($show);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $snippetTemplates = $paginator->paginate($snippetTemplates, array(
            'distinct' => false
        ));

        return $snippetTemplates;
    }

    /**
     * Get SnippetTemplate
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the SnippetTemplate is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="SnippetTemplate id"},
     *         {"name"="show", "dataType"="string", "required"=false, "description"="Define which SnippetTemplates to show, 'enabled', 'disabled', 'all'. Defaults to 'enabled'"}
     *     },
     *     output="\Newscoop\Entity\SnippetTemplate"
     * )
     *
     * @Route("/snippetTemplates/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     *
     * @return array
     */
    public function getSingleSnippetTemplateAction(Request $request, $id)
    {
        $show = $request->query->get('show', 'enabled');
        $em = $this->container->get('em');

        $snippetTemplate = $em->getRepository('Newscoop\Entity\Snippet\SnippetTemplate')
            ->getTemplateById($id, $show);

        if (!$snippetTemplate) {
            throw new EntityNotFoundException('Result was not found.');
        }

        return $snippetTemplate;
    }

    /**
     * Delete SnippetTemplate
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when SnippetTemplate removed succesfuly",
     *         404={
     *           "Returned when the SnippetTemplate is not found",
     *         },
     *         409="Returned when SnippetTemplate is used by Articles"
     *     },
     *     parameters={
     *         {"name"="force", "dataType"="boolean", "required"=false, "description"="Force delete"},
     *     }
     * )
     *
     * @Route("/snippetTemplates/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("DELETE")
     * @View(statusCode=204)
     */
    public function deleteSnippetTemplateAction(Request $request, $id)
    {
        $force = $request->query->get('force', false);
        $em = $this->container->get('em');
        $articleSnippets = $em->getRepository('Newscoop\Entity\Snippet\SnippetTemplate')
            ->deleteSnippetTemplate($id, $force);
    }

    /**
     * Create new SnippetTemplate
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when SnippetTemplate created succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\SnippetTemplateType"
     * )
     *
     * @Route("/snippetTemplates.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("POST")
     * @View()
     *
     * @return Form
     */
    public function createSnippetTemplateAction(Request $request)
    {
        return $this->processForm($request, null);
    }

    /**
     * Edit existing SnippetTemplate
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when SnippetTemplate edited succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\SnippetTemplateType"
     * )
     *
     * @Route("/snippetTemplates/{snippetTemplateId}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("PATCH|POST")
     * @View()
     *
     * @return Form
     */
    public function editSnippetTemplateAction(Request $request, $snippetTemplateId)
    {
        return $this->processForm($request, $snippetTemplateId);
    }

    /**
     * Process SnippetTemplate form
     *
     * @param Request $request
     * @param integer $snippetTemplateId
     *
     * @return Form
     */
    private function processForm($request, $snippetTemplateId = null)
    {
        // XXX It breaks using PATCH
        $em = $this->container->get('em');
        $patch = false;
        if (!is_null($snippetTemplateId) && !is_numeric($snippetTemplateId)) {
            throw new InvalidArgumentException("Parameter 'template' is not numeric");
        }

        if (!$snippetTemplateId) {
            $snippetTemplate = new SnippetTemplate();
            $statusCode = 201;
        } else {
            $snippetTemplate = $em->getRepository('Newscoop\Entity\Snippet\SnippetTemplate')
                ->getTemplateById($snippetTemplateId, 'all');
            $statusCode = 200;
            $patch = true;
            if (is_null($snippetTemplate)) {
                throw new InvalidArgumentException("Template with ID: '".$snippetTemplateId."' does not exist.");
            }
        }

        $form = $this->container->get('form.factory')->create(new SnippetTemplateType(array('patch'=>$patch)), $snippetTemplate);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $snippetTemplate = $form->getData();
            $em->getRepository('Newscoop\Entity\Snippet\SnippetTemplate')
                ->save($snippetTemplate);
            $response = new Response();
            $response->setStatusCode($statusCode);

            $response->headers->set(
                'X-Location',
                $this->generateUrl('newscoop_gimme_snippettemplates_getsinglesnippettemplate', array(
                    'id' => $snippetTemplate->getId(),
                ), true)
            );

            return $response;
        }

        return $form;
    }
}