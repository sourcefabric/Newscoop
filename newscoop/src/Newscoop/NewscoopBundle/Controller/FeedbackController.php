<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Newscoop\Entity\Feedback;

class FeedbackController extends Controller
{
    /**
     * @Route("/admin/feedback")
     * @Template()
     */
    public function indexAction(Request $request)
    {}

    /**
     * @Route("/admin/feedback/setstatus", options={"expose"=true})
     * @Template()
     */
    public function setStatusAction(Request $request)
    {
        $em = $this->get('em');
        $status = $request->request->get('status');
        $feedback = $request->request->get('feedback');

        try {
            $feedbackRepository = $em->getRepository('Newscoop\Entity\Feedback');
            $feedback = $feedbackRepository->find($feedback);
            $feedbackRepository->setFeedbackStatus($feedback, $status);
            $em->flush();
        } catch (Exception $e) {
            return new JsonResponse(array(
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ));
        }

        return new JsonResponse(array(
            'status' => 200,
            'message' => 'succcesful'
        ));
    }

    /**
     * @Route("/admin/feedback/reply", options={"expose"=true})
     */
    public function replyAction(Request $request)
    {
        $em = $this->get('em');
        $emailService = $this->container->get('email');
        $feedbackRepository = $em->getRepository('Newscoop\Entity\Feedback');

        $fromEmail = $this->getUser()->getEmail();
        $feedbackId = $request->request->get('feedback_id');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');
        $feedback = $feedbackRepository->find($feedbackId);
        $toEmail = $feedback->getUser()->getEmail();

        try {
            $emailService->send($subject, $message, $fromEmail, $toEmail);
        } catch (\Exception $e) {
            return new JsonResponse(array(
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            ));
        }

        return new JsonResponse(array('status' => 200, 'message' => 'succcesful'));
    }

    /**
     * @Route("/admin/feedback/load", options={"expose"=true})
     */
    public function loadFeedbacksAction(Request $request)
    {
        $em = $this->get('em');
        $paginator = $this->get('knp_paginator');
        $queries = $request->query->get('queries', array('search' => ''));
        $order = $request->query->get('sorts', array());

        foreach ($order as $key => $value) {
            if ($value == 1) {
                $order[$key] = 'asc';
            } else {
                $order[$key] = 'desc';
            }
        }

        $filters = array();
        foreach ($queries as $key => $value) {
            $filterName = explode('|', $key);

            if (count($filterName) > 1) {
                $filters[$filterName[0]][] = $value;
            }
        }

        $allFeedbacks = $em->getRepository('Newscoop\Entity\Feedback')
            ->getAllFeedbacks($order, $queries['search'], $filters);

        $allFeedbacks = $paginator->paginate(
            $allFeedbacks,
            $request->query->get('page', 1),
            $request->query->get('perPage', 10)
        );

        $processedData = array();
        foreach ($allFeedbacks as $feedback) {
            $processedData[] = $this->processFeedback($feedback, $request);
        }

        return new Response(json_encode(array(
            'records' => $processedData,
            'queryRecordCount' => $allFeedbacks->getTotalItemCount(),
            'totalRecordCount'=> count($allFeedbacks->getItems())
        )));
    }

    private function processFeedback(Feedback $feedback, $request)
    {
        $em = $this->get('em');
        $zendRouter = \Zend_Registry::get('container')->getService('zend_router');
        $translator = \Zend_Registry::get('container')->getService('translator');

        $data = array(
            'id' => $feedback->getId(),
            'time_created' => $feedback->getTimeCreated(),
            'message' => $feedback->getMessage(),
            'subject' => $feedback->getSubject(),
            'publication' => $feedback->getPublication()->getName(),
            'section' => '',
            'article' => '',
            'url' => $feedback->getUrl(),
            'status' => $feedback->getStatus()
        );

        if ($feedback->getSection()) {
            $data['section'] = $feedback->getSection()->getName();
        } else {
            $data['section'] = $translator->trans("empty");
        }

        if ($feedback->getArticle()) {
            $data['article'] = $feedback->getArticle()->getName();
        } else {
            $data['article'] = $translator->trans("empty");
        }

        if ($feedback->getAttachmentId()) {
            $attachment = $em->getRepository('Newscoop\Entity\Attachment')
                ->getAttachment($feedback->getAttachmentId())
                ->getOneOrNullResult();

            if ($attachment) {
                $attachmentService = $this->container->get('attachment');
                $data['attachment'] = array(
                    'name' => $attachment->getName(),
                    'type' => $feedback->getAttachmentType(),
                    'description' => $attachment->getDescription()->getTranslationText(),
                    'status' => $attachment->getStatus(),
                    'location' => $attachmentService->getAttachmentUrl($attachment).'?g_show_in_browser=true'
                );
            }
        }

        if ($feedback->getUser()) {
            $acceptanceRepository = $em->getRepository('Newscoop\Entity\Comment\Acceptance');

            $banned = $acceptanceRepository->checkBanned(array(
                'name' => $feedback->getUser()->getName(),
                'email' => '',
                'ip' => ''
            ), $publication);

            if ($banned['name'] == true) {
                $banned = true;
            } else {
                $banned = false;
            }

            $data['user'] = array(
                'name' => $feedback->getUser()->getFirstName(),
                'image' => $feedback->getUser()->getImage(),
                'is_banned' => $banned,
                'user_url' => $this->container->get('request')->getUriForPath($zendRouter->assemble(array_merge(array(
                    'module' => 'default',
                    'controller' => 'user',
                    'action' => 'profile',
                )), 'default', true).'/'.strip_tags($feedback->getUser()->getUsername())),
                'banurl' => $this->container->get('request')->getUriForPath($zendRouter->assemble(array(
                    'controller' => 'user',
                    'action' => 'toggle-ban',
                    'user' => $feedback->getUser()->getId(),
                    'publication' => $feedback->getPublication()->getId()
                ), 'admin', true)),
                'username' => strip_tags($feedback->getUser()->getUsername()),
                'email' => $feedback->getUser()->getEmail(),
            );
        } else {
            $data['user'] = 'Anonymous';
        }

        return $data;
    }
}
