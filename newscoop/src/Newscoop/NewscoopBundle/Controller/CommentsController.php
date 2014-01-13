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
use Newscoop\EventDispatcher\Events\GenericEvent;
use Newscoop\Entity\Comment;
use Newscoop\NewscoopBundle\Form\Type\CommentsFilterType;

/**
 * Comments controller.
 */
class CommentsController extends Controller
{
    /**
     * @Route("/admin/comments", options={"expose"=true})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->container->get('em');
        $translator = $this->container->get('translator');
        $commentService = $this->container->get('newscoop_newscoop.comments_service');
        $queryBuilder = $em->getRepository('Newscoop\Entity\Comment')
            ->createQueryBuilder('c');

        $queryBuilder
            ->select('c', 'cm.name', 't.name')
            ->leftJoin('c.commenter', 'cm')
            ->leftJoin('c.thread', 't')
            ->where($queryBuilder->expr()->isNotNull('c.article_num'))
            ->andWhere('c.status != 3') //3 - deleted
            ->orderBy('c.time_created', 'desc');

        $session = $request->getSession();
        $filterForm = $this->container->get('form.factory')->create(new CommentsFilterType(), array(), array());
        $statusMap = array(
            'approved' => 0,
            'new' => 1,
            'hidden' => 2,
        );

        //$session->set('commentsFilters', array());
        $filterForm->handleRequest($request);
        $filtersArray = array();
        if ($filterForm->isValid()) {
            $data = $filterForm->getData();
            $session->set('commentsFilters', null);
            if ($data['new']) {
                //$commentService->checkFilter($statusMap['new'], $queryBuilder);
                $queryBuilder->andWhere($queryBuilder->expr()->eq('c.status', $statusMap['new']));
                $filtersArray['filterNew'] = $statusMap['new'];
            }

            //$session->set('filterRecommended', null);
            if ($data['recommended']) {
                //$commentService->checkFilterRecommended($data['recommended'], $queryBuilder);
                $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', $data['recommended']));
                $filtersArray['filterRecommended'] = $data['recommended'];
            }

           // $session->set('filterUnrecommended', null);
            if ($data['unrecommended']) {
                //$commentService->checkFilterRecommended($data['unrecommended'], $queryBuilder);
                $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', $data['unrecommended']));
                $filtersArray['filterUnrecommended'] = $data['unrecommended'];
            }

            //$session->set('filterHidden', null);
            if ($data['hidden']) {
                //$commentService->checkFilter($statusMap['hidden'], $queryBuilder);
                $queryBuilder->andWhere($queryBuilder->expr()->eq('c.status', $statusMap['hidden']));
                $filtersArray['filterHidden'] = $statusMap['hidden'];
            }

            //$session->set('filterApproved', null);
            if ($data['approved']) {
                //$commentService->checkFilter($statusMap['approved'], $queryBuilder);
                $queryBuilder->andWhere($queryBuilder->expr()->eq('c.status', $statusMap['approved']));
                $filtersArray['filterApproved'] = $statusMap['approved'];
            }

            if ($data['recommended'] && $data['unrecommended']) {
                $queryBuilder->andWhere($queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('c.recommended', $data['unrecommended']),
                    $queryBuilder->expr()->eq('c.recommended', $data['recommended'])
                ));
            }
        }
try {
        if (array_key_exists('filterNew', $session->get('commentsFilters'))) {
            //$commentService->checkFilter($session->get('commentsFilters')['filterNew'], $queryBuilder);
            $queryBuilder->orWhere($queryBuilder->expr()->eq('c.status', $session->get('commentsFilters')['filterNew']));
        }
        //$commentService->checkFilter(null, $queryBuilder);
        if (array_key_exists('filterHidden', $session->get('commentsFilters'))) {
            //$commentService->checkFilter($session->get('commentsFilters')['filterHidden'], $queryBuilder);
            $queryBuilder->orWhere($queryBuilder->expr()->eq('c.status', $session->get('commentsFilters')['filterHidden']));
        }
       // $commentService->checkFilter(null, $queryBuilder);
        if (array_key_exists('filterApproved', $session->get('commentsFilters'))) {
            //var_dump($session->get('commentsFilters')['filterApproved']);
            //$commentService->checkFilter($session->get('commentsFilters')['filterApproved'], $queryBuilder);
            $queryBuilder->orWhere($queryBuilder->expr()->eq('c.status', $session->get('commentsFilters')['filterApproved']));
        }
        if (array_key_exists('filterRecommended', $session->get('commentsFilters'))) {
            //$commentService->checkFilterRecommended($session->get('commentsFilters')['filterRecommended'], $queryBuilder);
            $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', $session->get('commentsFilters')['filterRecommended']));
        }
        if (array_key_exists('filterUnrecommended', $session->get('commentsFilters'))) {
            //$commentService->checkFilterRecommended($session->get('commentsFilters')['filterUnrecommended'], $queryBuilder);
            $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', $session->get('commentsFilters')['filterUnrecommended']));
        }

        if (array_key_exists('filterUnrecommended', $session->get('commentsFilters')) && array_key_exists('filterRecommended', $filtersArray)) {
            //$commentService->checkFilterRecommended($session->get('commentsFilters')['filterUnrecommended'], $queryBuilder);
            $queryBuilder->andWhere($queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq('c.recommended', $session->get('commentsFilters')['filterRecommended']),
                $queryBuilder->expr()->eq('c.recommended', $session->get('commentsFilters')['filterUnrecommended'])
            ));
        }
    }catch(\Exception $e) {

    }
        $session->set('commentsFilters', $filtersArray);
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
                'banned' => $commentService->isBanned($comment[0]->getCommenter()),
                'avatarHash' => md5($comment[0]->getCommenter()->getEmail()),
                'issueNumber' => $comment[0]->getThread()->getSection()->getIssue()->getNumber(),
                'comment' => $comment[0],
                'index' => $counter,
            );

            $counter++;
        }

        $pagination->setTemplate('NewscoopNewscoopBundle:Pagination:pagination_bootstrap3.html.twig');

        return array(
            'pagination' => $pagination,
            'commentsArray' => $commentsArray,
            'filterForm' => $filterForm->createView(),
        );
    }

    /**
     * @Route("/admin/comments/set-status", options={"expose"=true})
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
                        $message = $translator->trans('comments.msg.error.deletefromarticle', array('$1' => $user->getCurrentUser()->getName(),
                                    '$2' => $comment->getThread()->getName(), '$3' => $comment->getLanguage()->getCode()), 'new_comments');
                    } else {
                        $message = $translator->trans('comments.msg.commentinarticle', array('$1' => $user->getCurrentUser()->getName(),
                                    '$2' => $comment->getThread()->getName(), '$3' => $comment->getLanguage()->getCode(), '$4' => $status), 'new_comments');
                    }
                }

                $em->getRepository('Newscoop\Entity\Comment')->setStatus($comments, $status);
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('message' => $e->getMessage()));
            }

            return new JsonResponse(array(
                'message' => $message,
                'comments' => $comments,
                'status' => $status
            ));
        }
    }

    /**
     * @Route("/admin/comments/reply/{id}", options={"expose"=true})
     */
    public function replyAction(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $em = $this->container->get('em');
            $values = $request->request->all();
            $comment = new Comment();

            if (!$values['subject'] || !$values['message']) {
                return new JsonResponse(array('status' => false));
            }

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
                    'status' => $e->getMessage()
                ));
            }

            return new JsonResponse(array(
                'status' => true
            ));
        }
    }

    /**
     * @Route("/admin/comments/set-recommended/{comments}/{recommended}", options={"expose"=true})
     */
    public function setRecommendedAction(Request $request, $comments, $recommended)
    {
        if ($request->isMethod('POST')) {
            $em = $this->container->get('em');
            if (!is_array($comments)) {
                $comments = array($comments);
            }

            foreach ($comments as $commentId) {
                if (!$recommended) {
                    continue;
                }

                $comment = $em->getRepository('Newscoop\Entity\Comment')->find($commentId);

                $this->container->get('dispatcher')->dispatch('comment.recommended', new GenericEvent($this, array(
                    'id' => $comment->getId(),
                    'subject' => $comment->getSubject(),
                    'article' => $comment->getThread()->getName(),
                    'commenter' => $comment->getCommenterName(),
                )));
            }

            try {
                $em->getRepository('Newscoop\Entity\Comment')->setRecommended($comments, $recommended);
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('status' => $e->getMessage()));
            }

            return new JsonResponse(array('status' => true));
        }
    }

    /**
     * @Route("/admin/comments/update/{id}", options={"expose"=true})
     */
    public function updateAction(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $em = $this->container->get('em');
            $values = $request->request->all();
            if (!$values['subject'] || !$values['message']) {
                return new JsonResponse(array('status' => false));
            }

            try {
                $comment = $em->getRepository('Newscoop\Entity\Comment')->find($id);
                $em->getRepository('Newscoop\Entity\Comment')->update($comment, $values);
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array('status' => $e->getMessage()));
            }

            return new JsonResponse(array(
                'status' => true,
                'comment' => $id,
                'subject' => $values['subject'],
                'message' => $values['message']
            ));
        }
    }
}
