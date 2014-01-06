<?php
/**
 * @package   Newscoop\NewscoopBundle
 * @author    Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

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

        //if ($filterForm->isValid()) {
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
        //}

        $comments = $queryBuilder->getQuery()->getResult();
        $counter = 1;
        $commentsArray = array();
        foreach ($comments as $comment) {
            $commentsArray[] = array(
                'banned' => $this->isBanned($em, $comment->getCommenter(), null),
                'avatarHash' => md5($comment->getCommenter()->getEmail()),
                'issueNumber' => $comment->getThread()->getSection()->getIssue()->getNumber(),
                'comment' => $comment,
                'index' => $counter,
            );

            $counter++;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $commentsArray,
            $this->get('request')->query->get('knp_page', 1),
            10
        );

        $pagination->setTemplate('NewscoopNewscoopBundle:Pagination:pagination_bootstrap3.html.twig');

        return array(
            'pagination' => $pagination,
            'filterForm' => $filterForm->createView(),
            'defaultValues' => $defaultValues
        );
    }

    /**
     * Checks if a commenter is banned
     *
     * @param EntityManager            $em            Entity Manager
     * @param Newcoop\Entity\Commenter $commenter     Commenter
     * @param int                      $publicationId Publication id
     *
     * @return bool
     */
    public function isBanned($em, $commenter, $publicationId)
    {
        $queryBuilder = $em->getRepository('Newscoop\Entity\Comment\Acceptance')
            ->createQueryBuilder('a');

        $queryBuilder->where($queryBuilder->expr()->orX(
            $queryBuilder->expr()->eq('a.search', ':name'),
            $queryBuilder->expr()->eq('a.search', ':email'),
            $queryBuilder->expr()->eq('a.search', ':ip')
        ));

        $queryBuilder->setParameters(array(
            'name' => $commenter->getName(),
            'email' => $commenter->getEmail(),
            'ip' => $commenter->getIp()
        ));

        $query = $queryBuilder->getQuery()->getResult();

        if ($query) {
            return true;
        }

        return false;
    }
}