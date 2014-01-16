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
use Newscoop\NewscoopBundle\Form\Type\CommentSearchType;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Comments controller.
 */
class CommentsController extends Controller
{
    /**
     * @Route("/admin/comments", options={"expose"=true})
     * @Route("/admin/comments/search", name="newscoop_newscoop_comments_search",  options={"expose"=true})
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->container->get('em');
        $translator = $this->container->get('translator');
        $paginator = $this->get('knp_paginator');
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
        $pageNumber = $this->get('request')->query->get('knp_page', 1);

        $filters = new ParameterBag();
        $filterForm = $this->container->get('form.factory')->create(new CommentsFilterType(), array(), array());
        $searchForm = $this->container->get('form.factory')->create(new CommentSearchType(), array(), array());
        $or = $queryBuilder->expr()->orX();
        $and = $queryBuilder->expr()->andX();

        if ($request->get('_route') === "newscoop_newscoop_comments_search") {
            $searchForm->handleRequest($request);
            if ($request->isMethod('POST')) {
                if ($searchForm->isValid()) {
                    $data = $searchForm->getData();
                    $comments = $commentService->searchByPhrase($data['search'])->getQuery();
                    $pagination = $paginator->paginate($comments, $pageNumber, 10);
                    $pagination->setTemplate('NewscoopNewscoopBundle:Pagination:pagination_bootstrap3.html.twig');

                    return array(
                        'pagination' => $pagination,
                        'commentsArray' => $commentService->createCommentsArray($pagination),
                        'filterForm' => $filterForm->createView(),
                        'searchForm' => $searchForm->createView()
                    );
                }
            }
        }

        $filterForm->handleRequest($request);
        if ($filterForm->isValid()) {
            $data = $filterForm->getData();
            $pageNumber = 1;
            // if more than one filter applied
            if (count(array_filter($data)) > 1) {
                if ($data['recommended'] && $data['unrecommended']) {
                    $queryBuilder->andWhere($queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq('c.recommended', 1),
                        $queryBuilder->expr()->eq('c.recommended', 0)
                    ));

                    $filters->set('filterRecommended', $data['recommended']);
                    $filters->set('filterUnrecommended', $data['unrecommended']);
                } else {
                    if ($data['recommended']) {
                        $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', 1));
                        $filters->set('filterRecommended', $data['recommended']);
                    }

                    if ($data['unrecommended']) {
                        $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', 0));
                        $filters->set('filterUnrecommended', $data['unrecommended']);
                    }
                }
                unset($data['recommended']);
                unset($data['unrecommended']);
                $queryBuilder->andWhere($commentService->buildFilterQuery($data, $or, $filters, $queryBuilder));
            } else {
                if ($data['recommended'] && $data['unrecommended']) {
                    $queryBuilder->andWhere($queryBuilder->expr()->orX(
                        $queryBuilder->expr()->eq('c.recommended', 1),
                        $queryBuilder->expr()->eq('c.recommended', 0)
                    ));

                    $filters->set('filterRecommended', $data['recommended']);
                    $filters->set('filterUnrecommended', $data['unrecommended']);
                } else {
                    if ($data['recommended']) {
                        $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', 1));
                        $filters->set('filterRecommended', $data['recommended']);
                    }

                    if ($data['unrecommended']) {
                        $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', 0));
                        $filters->set('filterUnrecommended', $data['unrecommended']);
                    }
                }

                unset($data['recommended']);
                unset($data['unrecommended']);
                $queryBuilder->andWhere($commentService->buildFilterQuery($data, $and, $filters, $queryBuilder));
            }

            $session->set('commentsFilters', $filters);
        } else {
            if ($session->get('commentsFilters')) {
                // if more than one filter applied
                if ($session->get('commentsFilters')->count() > 1) {
                    if ($session->get('commentsFilters')->get('filterRecommended') && $session->get('commentsFilters')->get('filterUnrecommended')) {
                        $queryBuilder->andWhere($queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq('c.recommended', 1),
                            $queryBuilder->expr()->eq('c.recommended', 0)
                        ));
                    } else {
                        if ($session->get('commentsFilters')->get('filterRecommended')) {
                            $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', 1));
                        }

                        if ($session->get('commentsFilters')->get('filterUnrecommended')) {
                            $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', 0));
                        }
                    }

                    $queryBuilder->andWhere($commentService->buildSessionFilters($session->get('commentsFilters'), $or, $queryBuilder));
                } else {
                    if ($session->get('commentsFilters')->get('filterRecommended') && $session->get('commentsFilters')->get('filterUnrecommended')) {
                        $queryBuilder->andWhere($queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq('c.recommended', 1),
                            $queryBuilder->expr()->eq('c.recommended', 0)
                        ));
                    } else {
                        if ($session->get('commentsFilters')->get('filterRecommended')) {
                            $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', 1));
                        }

                        if ($session->get('commentsFilters')->get('filterUnrecommended')) {
                            $queryBuilder->andWhere($queryBuilder->expr()->eq('c.recommended', 0));
                        }
                    }

                    $queryBuilder->andWhere($commentService->buildSessionFilters($session->get('commentsFilters'), $and, $queryBuilder));
                }
            }
        }

        if (!$session->get('commentsFilters', null)) {
            $session->set('commentsFilters', $filters);
        }

        $comments = $queryBuilder->getQuery();
        $pagination = $paginator->paginate($comments, $pageNumber, 10);
        $pagination->setTemplate('NewscoopNewscoopBundle:Pagination:pagination_bootstrap3.html.twig');

        return array(
            'pagination' => $pagination,
            'commentsArray' => $commentService->createCommentsArray($pagination),
            'filterForm' => $filterForm->createView(),
            'searchForm' => $searchForm->createView()
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
