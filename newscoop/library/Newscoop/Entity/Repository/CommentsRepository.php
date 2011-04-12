<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder,
    Newscoop\Entity\Comments,
    Newscoop\Entity\CommentsUsers,
    Newscoop\Datatable\Source as DatatableSource;

/**
 * Comments users repository
 */
class CommentsRepository extends DatatableSource
{

    /**
     * Method for setting status
     *
     * @param array $p_entity_ids
     * @param string $status
     * @return mixed
     */
    public function setStatus(array $p_comments_ids, $status)
    {
        $em = $this->getEntityManager();
        foreach($p_comments_ids as $comment_id)
        {
            $comment = $this->find($comment_id);
            $comment->setStatus($status);
            $em->persist($comment);
        }
    }
    /**
     * Method for saving a comment
     *
     * @param Newscoop\Entity\Comments $p_enitity
     * @param array $params
     * @return Newscoop\Entity\Comments $p_enitity
     */
    public function save($p_entity, $p_values)
    {
        // get the enitity manager
        $em = $this->getEntityManager();

        $commentsUserRepository = $em->getRepository('Newscoop\Entity\CommentsUsers');
        $commentsUser = new CommentsUsers;
        $commentsUser = $commentsUserRepository->save($commentsUser, $p_values);

        $p_entity->setUser($commentsUser)
                 ->setSubject($p_values['subject'])
                 ->setMessage($p_values['message'])
                 ->setStatus($p_values['status'])
                 ->setIp($p_values['ip'])
                 ->setTimeCreated($p_values['time_created']);
        $threadLevel = 0;
        if(!empty($p_values['parent']) && (0 != $p_values['parent']))
        {
            $parent = $this->find($p_values['parent']);
            // set parent of the comment
            $p_entity->setParent($parent);
            /**
             * get the maximum thread order from the current parent
             */
            $qb = $this->createQueryBuilder('c');
            $threadOrder = $qb->select('MAX(c.thread_order)')
               ->andwhere('c.parent = :parent')
               ->andWhere('c.thread = :thread')
               ->andWhere('c.language = :language')
               ->setParameter('parent', $parent)
               ->setParameter('thread', $parent->getThread())
               ->setParameter('language', $parent->getLanguage())
               ->getQuery()
               ->getSingleScalarResult();
            // if the comment parent doesn't have children then use the parent thread order
            if(!$threadOrder)
                $threadOrder = $parent->getThreadOrder();
            $threadOrder+= 1;
            /**
             * update all the comments for the thread where thread order is less or equal
             * of the current thread_order
             */
            $qb = $this->createQueryBuilder('c');
            $qb->update()
               ->set('c.thread_order',$qb->expr()->literal('c.thread_order+1'))
               ->andwhere('c.thread_order >= :thread_order')
               ->andWhere('c.thread = :thread')
               ->andWhere('c.language = :language')
               ->setParameter('language', $parent->getLanguage())
               ->setParameter('thread', $parent->getThread())
               ->setParameter('thread_order', $thread_order);
           // set the thread level the thread level of the parent plus one the current level
           $threadLevel = $parent->getThreadLevel()+1;
        }
        else
        {
            $threadOrder = $qb->select('MAX(c.thread_order)')
               ->andWhere('c.thread = :thread')
               ->andWhere('c.language = :language')
               ->setParameter('thread', $thread)
               ->setParameter('language', $language)
               ->getQuery()
               ->getSingleScalarResult();
            // increase by one of the current comment
            $threadOrder+= 1;
            $articlesRepository = $em->getRepository('Newscoop\Entity\Articles');
            $publicationsRepository = $em->getRepository('Newscoop\Entity\Publications');
            $languagesRepository = $em->getRepository('Newscoop\Entity\Languages');

            $thread = $articlesRepository->find($p_values['thread_id']);
            $forum = $publicationsRepository->find($p_values['forum_id']);
            $language = $languagesRepository->find($p_values['language_id']);

            $p_entity->setLanguage($language)
                     ->setForum( $forum )
                     ->setThread( $thread );

        }
        $p_entity->setThreadOrder($threadOrder)
                 ->setThreadLevel($threadLevel);

        $em->persist($p_entity);
        return $p_entity;
    }


    /**
     * Build where condition
     *
     * @param array $cols
     * @param string $search
     * @return Doctrine\ORM\Query\Expr
     */
    protected function buildWhere(array $cols, $search)
    {
        $qb = $this->createQueryBuilder('e');
        $or = $qb->expr()->orx();
        foreach (array_keys($cols) as $i => $property) {
            if (!is_string($property)) { // not searchable
                continue;
            }
            if("user" == $property)
            {
                //$or->add($qb->expr()->like("u.$property", $qb->expr()->literal("%{$search}%")));
            }
            elseif("thread" == $property)
            {
            }
            else
                $or->add($qb->expr()->like("e.$property", $qb->expr()->literal("%{$search}%")));
        }
        return $or;
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

}
