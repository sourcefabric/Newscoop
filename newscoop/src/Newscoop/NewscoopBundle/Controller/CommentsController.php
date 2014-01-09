<?php
/**
 * @package   Newscoop\NewscoopBundle
 * @author    Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Newscoop\NewscoopBundle\Form\Type\CommentButtonsType;
use Newscoop\Entity\Comment;

/**
 * Comments controller.
 */
class CommentsController extends Controller
{
    /**
     * @Route("/admin/comments")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->container->get('em');
        $commentService = $this->container->get('newscoop_newscoop.comments_service');
        $queryBuilder = $em->getRepository('Newscoop\Entity\Comment')
            ->createQueryBuilder('c')
            ->orderBy('c.time_created', 'desc');

        $defaultValues = array(
            'new' => false,
            'approved' => false,
            'hidden' => false,
            'recommended' => false,
            'unrecommended' => false,
        );

        $buttonsForm = $this->container->get('form.factory')->create(new CommentButtonsType(), array(
            'buttons' => 'pending'
        ), array());

        $filterForm = $this->createFormBuilder()
            ->add('new', 'checkbox', array(
                'required'  => false,
            ))
            ->add('approved', 'checkbox', array(
                'required'  => false,
            ))
            ->add('hidden', 'checkbox', array(
                'required'  => false,
            ))
            ->add('recommended', 'checkbox', array(
                'required'  => false,
            ))
            ->add('unrecommended', 'checkbox', array(
                'required'  => false,
            ))
            ->add('filterButton', 'submit')
            ->getForm();

        $filterForm->handleRequest($request);

        if ($filterForm->isValid()) {
            $data = $filterForm->getData();

            if ($data['new']) {
                $queryBuilder
                    ->where('c.status = :status')
                    ->setParameter('status', 'approved');
            }

            if ($data['recommended']) {
                $queryBuilder
                    ->where('c.recommended = :status')
                    ->setParameter('status', $data['recommended']);
            }
        }

        $comments = $queryBuilder->getQuery();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $comments,
            $this->get('request')->query->get('knp_page', 1),
            10
        );

        $counter = 1;
        $commentsArray = array();
        foreach ($pagination as $comment) {
            $commentsArray[] = array(
                'banned' => $commentService->isBanned($comment->getCommenter()),
                'avatarHash' => md5($comment->getCommenter()->getEmail()),
                'issueNumber' => $comment->getThread()->getSection()->getIssue()->getNumber(),
                'comment' => $comment,
                'index' => $counter,
            );

            $counter++;
        }

        $pagination->setTemplate('NewscoopNewscoopBundle:Pagination:pagination_bootstrap3.html.twig');

        return array(
            'pagination' => $pagination,
            'commentsArray' => $commentsArray,
            'filterForm' => $filterForm->createView(),
            'defaultValues' => $defaultValues,
            'buttonsForm' => $buttonsForm->createView(),
        );
    }

    /**
     * @Route("/admin/comments/set-status")
     */
    public function setStatusAction(Request $request) 
    {
        $translator = $this->container->get('translator');
        $user = $this->container->get('user');
        $em = $this->container->get('em');
        $commentService = $this->container->get('newscoop_newscoop.comments_service');
        $status = $request->request->get('status');
        $comments = $request->request->get('comment');

        if ($request->isMethod('POST')) {
            if (!is_array($comments)) {
                $comments = array($comments);
            }

            if ($status == "deleted") {
                $comments = array_unique(array_merge($comments, $commentService->getAllReplies($comments)));
            }

            try {
                foreach ($comments as $id) {
                    $comment = $em->getRepository('Newscoop\Entity\Comment')->find($id);
                    if ($status == "deleted") {
                        $msg = $translator->trans('comments.msg.error.deletefromarticle', array('$1' => $user->getCurrentUser->getName(),
                                    '$2' => $comment->getThread()->getName(), '$3' => $comment->getLanguage()->getCode()), 'new_comments');
                    } else {
                        $msg = $translator->trans('comments.msg.commentinarticle', array('$1' => $user->getCurrentUser()->getName(),
                                    '$2' => $comment->getThread()->getName(), '$3' => $comment->getLanguage()->getCode(), '$4' => $status), 'new_comments');
                    }
                }

                $em->getRepository('Newscoop\Entity\Comment')->setStatus($comments, $status);
                $em->flush();
            } catch (\Exception $e) {
                return;
            }

            return new JsonResponse(array('message' => $msg));
        }
    }

    /**
     * @Route("/admin/comments/reply/{id}")
     */
    public function replyAction(Request $request, $id)
    {
        $translator = $this->container->get('translator');
        $em = $this->container->get('em');

        $values = $request->request->all();
        $comment = new Comment();

        if ($request->isMethod('POST')) {
            $values['parent'] = $id;
            $values['user'] = $this->container->get('user')->getCurrentUser();
            $values['time_created'] = new \DateTime();
            $values['ip'] = $request->getClientIp();
            $values['status'] = 'approved';

            try {
                $comment = $em->getRepository('Newscoop\Entity\Comment')->save($comment, $values);
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array(
                    'status' => false
                ));
            }

            return new JsonResponse(array(
                'status' => true
            ));
        }
    }
}