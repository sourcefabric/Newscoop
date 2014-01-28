<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Newscoop\Entity\Attachment;
use Newscoop\Entity\User;
use Newscoop\GimmeBundle\Form\Type\AttachmentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Attachment controller
 */
class AttachmentsController extends FOSRestController
{
    /**
     * Get all attachments
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the attachments are not found"
     *         }
     *     }
     * )
     *
     * @Route("/attachments.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getAttachmentsAction(Request $request)
    {
        $em = $this->container->get('em');

        $attachments = $em->getRepository('Newscoop\Entity\Attachment')
            ->getAttachments();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $attachments = $paginator->paginate($attachments, array(
            'distinct' => false
        ));

        return $attachments;
    }

    /**
     * Get attachment
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the attachment is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Attachment id"}
     *     },
     *     output="\Newscoop\Entity\Attachment"
     * )
     *
     * @Route("/attachments/{number}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     *
     * @return Form
     */
    public function getAttachmentAction(Request $request, $number)
    {
        $em = $this->container->get('em');

        $attachment = $em->getRepository('Newscoop\Entity\Attachment')
            ->getAttachment($number)
            ->getOneOrNullResult();

        if (!$attachment) {
            throw new EntityNotFoundException('Result was not found.');
        }

        return $attachment;
    }

    /**
     * Create new attachment
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when attachment created succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\AttachmentType"
     * )
     *
     * @Route("/attachments.{_format}", defaults={"_format"="json"})
     * @Method("POST")
     * @View()
     *
     * @return Form
     */
    public function createAttachmentAction(Request $request)
    {
        return $this->processForm($request);
    }

    /**
     * Update attachment
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when attachment updated succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\AttachmentType",
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Attachment id"}
     *     }
     * )
     *
     * @Route("/attachments/{number}.{_format}", defaults={"_format"="json"})
     * @Method("POST|PATCH")
     * @View()
     *
     * @return Form
     */
    public function updateAttachmentAction(Request $request, $number)
    {
        return $this->processForm($request, $number);
    }

    /**
     * Delete image
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when attachment removed succesfuly",
     *         404={
     *           "Returned when the attachment is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Attachment id"}
     *     }
     * )
     *
     * @Route("/attachments/{number}.{_format}", defaults={"_format"="json"})
     * @Method("DELETE")
     * @View(statusCode=204)
     *
     * @return Form
     */
    public function deleteAttachmentAction(Request $request, $number)
    {
        $attachmentService = $this->container->get('attachment');
        $em = $this->container->get('em');
        $attachment = $em->getRepository('Newscoop\Entity\Attachment')->findOneById($number);

        if (!$attachment) {
            throw new EntityNotFoundException('Result was not found.');
        }

        $attachmentService->remove($attachment);
    }

    /**
     * Process attachment form
     *
     * @param Request $request
     * @param integer $attachment
     *
     * @return Form
     */
    private function processForm($request, $attachment = null)
    {
        $em = $this->container->get('em');
        $attachmentService = $this->container->get('attachment');

        if (!$attachment) {
            $statusCode = 201;
        } else {
            $statusCode = 200;
            $attachment = $em->getRepository('Newscoop\Entity\Attachment')->findOneById($attachment);

            if (!$attachment) {
                throw new EntityNotFoundException('Result was not found.');
            }
        }

        $form = $this->createForm(new AttachmentType(), array());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $file = $form['attachment']->getData();
            $attributes = $form->getData();
            $user = $this->getUser();

            if ($user) {
                $attributes['user'] = $user;
            }

            $language = $em->getRepository('Newscoop\Entity\Language')
                ->findOneById($attributes['language']);
            unset($attributes['language']);

            $attachment = $attachmentService->upload(
                $file,
                $attributes['description'],
                $language,
                $attributes,
                $attachment
            );

            $response = new Response();
            $response->setStatusCode($statusCode);

            $response->headers->set(
                'X-Location',
                $this->generateUrl('newscoop_gimme_attachments_getattachment', array(
                    'number' => $attachment->getId(),
                ), true)
            );

            return $response;
        }

        return $form;
    }
}
