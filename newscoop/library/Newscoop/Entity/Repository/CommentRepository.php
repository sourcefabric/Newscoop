<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Doctrine\ORM\QueryBuilder,
    Newscoop\Entity\Comment,
    Newscoop\Entity\Comment\Commenter,
    Newscoop\Datatable\Source as DatatableSource;

/**
 * Comment repository
 */
class CommentRepository extends DatatableSource
{

    /**
     * Method for setting status
     *
     * @param array $p_entity_ids
     * @param string $status
     * @return mixed
     */
    public function setStatus(array $p_comment_ids, $p_status)
    {
        $em = $this->getEntityManager();
        foreach($p_comment_ids as $comment_id)
        {
            $comment = $this->find($comment_id);
            $comment->setStatus($p_status);
            $em->persist($comment);
        }
    }

    public function getArticleComments($p_thread, $p_language, $p_params)
    {

        // get the enitity manager
        $em = $this->getEntityManager();

        $qb = $this->createQueryBuilder('e');
        $qb->select();

        $articleRepository = $em->getRepository('Newscoop\Entity\Article');
        $languageRepository = $em->getRepository('Newscoop\Entity\Language');

        $article = $articleRepository->find($p_thread);
        $language = $languageRepository->find($p_language);
        $qb->andWhere('e.thread = :thread')
            ->andWhere('e.forum = :forum')
            ->andWhere('e.language = :language')
            ->setParameter('thread', $article)
            ->setParameter('forum',$article->getPublication())
            ->setParameter('language',$language);

        return $qb->getQuery()->getResult();

    }

    /**
     * Method for saving a comment
     *
     * @param Comment $p_enitity
     * @param array $params
     * @return Comment $p_enitity
     */
    public function save(Comment $p_entity, $p_values)
    {
        // get the enitity manager
        $em = $this->getEntityManager();

        $commenterRepository = $em->getRepository('Newscoop\Entity\Comment\Commenter');
        $commenter = new Commenter;
        $commenter = $commenterRepository->save($commenter, $p_values);
        $p_entity->setCommenter($commenter)
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
             * update all the comment for the thread where thread order is less or equal
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
            $qb = $this->createQueryBuilder('c');
            $threadOrder = $qb->select('MAX(c.thread_order)')
               ->andWhere('c.thread = :thread')
               ->andWhere('c.language = :language')
               ->setParameter('thread', $thread)
               ->setParameter('language', $language)
               ->getQuery()
               ->getSingleScalarResult();
            // increase by one of the current comment
            $threadOrder+= 1;
            $articleRepository = $em->getRepository('Newscoop\Entity\Article');
            $languageRepository = $em->getRepository('Newscoop\Entity\Language');

            $thread = $articleRepository->find($p_values['thread_id']);
            $language = $languageRepository->find($p_values['language_id']);

            $p_entity->setLanguage($language)
                     ->setForum( $thread->getPublication() )
                     ->setThread( $thread );
        }
        $p_entity->setThreadOrder($threadOrder)
                 ->setThreadLevel($threadLevel);

        $em->persist($p_entity);
        return $p_entity;
    }

    /**
     * Get data for table
     *
     * @param array $params
     * @param array $cols
     * @return array
     */
    public function getData(array $p_params, array $p_cols)
    {
        $qb = $this->createQueryBuilder('e');
        if (!empty($p_params['sSearch'])) {
            $qb->where($this->buildWhere($p_cols, $p_params['sSearch']));
        }

        // sort
        foreach (array_keys($p_cols) as $id => $property) {
            if (!is_string($property)) { // not sortable
                continue;
            }

            if (isset($p_params["iSortCol_$id"])) {
                $dir = $p_params["sSortDir_$id"] ?: 'asc';
                $qb->orderBy("e.$property", $dir);
            }
        }

        if(isset($p_params['sFilter']))
        {
            foreach($p_params['sFilter'] as $key => $values)
            {
                if(is_array($values))
                {
                    $or = $qb->expr()->orx();
                    $mapper = array_flip($values);
                    if($key=='status')
                        $mapper = array_flip(Comment::$status_enum);
                    foreach($values as $value)
                        $or->add($qb->expr()->eq('e.'.$key, $mapper[$value]));
                    $qb->where($or);
                }
            }
        }

        // limit
        $qb->setFirstResult((int) $p_params['iDisplayStart'])
            ->setMaxResults((int) $p_params['iDisplayLength']);

        return $qb->getQuery()->getResult();
    }

    /**
     * Build where condition
     *
     * @param array $cols
     * @param string $search
     * @return Doctrine\ORM\Query\Expr
     */
    protected function buildWhere(array $p_cols, $p_search)
    {
        $qb = $this->createQueryBuilder('e');
        $or = $qb->expr()->orx();
        foreach (array_keys($p_cols) as $i => $property) {
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
                $or->add($qb->expr()->like("e.$property", $qb->expr()->literal("%{$p_search}%")));
        }
        return $or;
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

}
