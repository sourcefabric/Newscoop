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
use Newscoop\Entity\Feedback;
use Newscoop\Entity\User;
use Newscoop\GimmeBundle\Form\Type\FeedbackType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

/**
 * Feedbacks controller
 */
class FeedbacksController extends FOSRestController
{
    /**
     * Get feedback
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the feedback is not found"
     *         }
     *     }
     * )
     *
     * @Route("/feedbacks/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     *
     * @return array
     */
    public function getFeedbackAction(Request $request, $id)
    {
        $em = $this->container->get('em');

        $feedback = $em->getRepository('Newscoop\Entity\Feedback')
            ->getOneFeedback($id)
            ->getOneOrNullResult();

        if (!$feedback) {
            throw new EntityNotFoundException('Result was not found.');
        }

        return $feedback;
    }

    /**
     * Get all feedbacks
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the feedbacks are not found"
     *         }
     *     }
     * )
     *
     * @Route("/feedbacks.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getFeedbacksAction(Request $request)
    {
        $em = $this->container->get('em');

        $feedbacks = $em->getRepository('Newscoop\Entity\Feedback')
            ->getAllFeedbacks();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $feedbacks = $paginator->paginate($feedbacks, array(
            'distinct' => false
        ));

        return $feedbacks;
    }

    /**
     * Create new feedback
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when feedback created succesfuly"
     *     },
     *     input="\Newscoop\GimmeBundle\Form\Type\FeedbackType"
     * )
     *
     * @Route("/feedbacks.{_format}", defaults={"_format"="json"})
     * @Method("POST")
     * @View()
     *
     * @return Form
     */
    public function createFeedbackAction(Request $request)
    {
        return $this->processForm($request);
    }

    /**
     * Delete feedback
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when feedback removed succesfuly",
     *         404={
     *           "Returned when the feedback is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="number", "dataType"="integer", "required"=true, "description"="Feedback id"}
     *     }
     * )
     *
     * @Route("/feedbacks/{number}.{_format}", defaults={"_format"="json"})
     * @Method("DELETE")
     * @View(statusCode=204)
     *
     * @return Form
     */
    public function deleteFeedbackAction(Request $request, $number)
    {
        $em = $this->container->get('em');
        $feedbackRepository = $em->getRepository('Newscoop\Entity\Feedback');
        $feedback = $feedbackRepository->find($feedback);
        if (!$attachment) {
            throw new EntityNotFoundException('Result was not found.');
        }

        $feedbackRepository->setFeedbackStatus($feedback, 'deleted');
        $em->flush();
    }

    /**
     * Process feedback form
     *
     * @param Request $request
     *
     * @return Form
     */
    private function processForm($request)
    {
        $em = $this->container->get('em');
        $feedbackRepository = $em->getRepository('Newscoop\Entity\Feedback');
        $attachmentService = $this->container->get('attachment');
        $publicationService = $this->container->get('newscoop.publication_service');
        $statusCode = 201;

        $form = $this->createForm(new FeedbackType(), array());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $file = $form['attachment']->getData();
            $attributes = $form->getData();
            $attachmentAttributes = array();
            $user = $this->getUser();

            if ($user) {
                $attributes['user'] = $user;
                $attachmentAttributes['user'] = $user;
            }
            $attributes['publication'] = $publicationService->getPublication()->getId();
            $attributes['attachment_type'] = 'none';

            if ($file) {
                $attachmentService = $this->container->get('attachment');
                $attachment = $attachmentService->upload(
                    $file,
                    $attributes['subject'],
                    $publicationService->getPublication()->getLanguage(),
                    $attachmentAttributes
                );

                if ($attachment) {
                    $type = explode('/', $attachment->getMimeType());
                    if ($type[0] == 'image') {
                        $attributes['attachment_type'] = 'image';
                    } else {
                        $attributes['attachment_type'] = 'document';
                    }

                    $attributes['attachment_id'] = $attachment->getId();
                }
            }

            $feedback = $feedbackRepository->save(new Feedback(), $attributes);
            $em->flush();

            $response = new Response();
            $response->setStatusCode($statusCode);

            $response->headers->set(
                'X-Location',
                $this->generateUrl('newscoop_gimme_feedbacks_getfeedback', array(
                    'id' => $feedback->getId(),
                ), true)
            );

            return $response;
        }

        return $form;
    }
}
