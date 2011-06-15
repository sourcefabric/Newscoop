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
     * Get new instance of the comment
     */
    public function getPrototype()
    {
        return new Comment;
    }

    /**
     * Method for setting status
     *
     * @param array $p_comment_ids
     * @param string $p_status
     * @return void
     */
    public function setStatus(array $p_comment_ids, $p_status)
    {
        $em = $this->getEntityManager();
        foreach($p_comment_ids as $comment_id)
            $this->setCommentStatus($this->find($comment_id), $p_status);
    }

    /**
     * Method for setting status per article
     *
     * @param int $p_article
     * @param string $p_status
     * @return void
     */
    public function setArticleStatus($p_article, $p_language, $p_status)
    {
        $em = $this->getEntityManager();
        $params = array('thread' => $p_article, 'language' => $p_language);
        if($p_status == 'hidden')
            $params['status'] = 0;
        elseif($p_status == 'approved')
            $params['status'] = 2;
        $comments = $this->findBy($params);
        foreach($comments as $comment) {
            $this->setCommentStatus($comment, $p_status);
        }

    }

    /**
     * Method for setting status for a comment
     *
     * @param Comment $p_comment
     * @param string $p_status
     */
    private function setCommentStatus(Comment $p_comment, $p_status)
    {
        $em = $this->getEntityManager();
        if($p_status == 'deleted')
        {
            $em->remove($p_comment);
        }
        else
        {
            $p_comment->setStatus($p_status);
            $em->persist($p_comment);
        }
    }

    /**
     * Method for update a comment
     *
     * @param Comment $p_enitity
     * @param array $params
     * @return Comment $p_enitity
     */
    public function update(Comment $p_entity, $p_values)
    {
        // get the enitity manager
        $em = $this->getEntityManager();
        $p_entity->setSubject($p_values['subject'])
                 ->setMessage($p_values['message'])
                 ->setTimeUpdated(new \DateTime);
        $em->persist($p_entity);
        return $p_entity;
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
            $p_entity->setParent($parent)
                     ->setLanguage($parent->getLanguage())
                     ->setForum( $parent->getForum() )
                     ->setThread( $parent->getThread() );
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
            $articleRepository = $em->getRepository('Newscoop\Entity\Article');
            $thread = $articleRepository->find($p_values['thread']);
            if (!isset($p_values['language'])) {
                $language = $thread->getLanguage();
            } else {
                $languageRepository = $em->getRepository('Newscoop\Entity\Language');
                $language = $languageRepository->find($p_values['language']);
            }

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
     * @param array $p_params
     * @param array $cols
     * @return Comment[]
     */
    public function getData(array $p_params, array $p_cols)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->leftJoin('e.commenter','c');
        $qb->leftJoin('e.thread','a');
        if (!empty($p_params['sSearch']))
            $qb->where($this->buildWhere($p_cols, $p_params['sSearch'], $qb));

        if (!empty($p_params['sFilter']))
            $qb->where($this->buildFilter($p_cols, $p_params['sFilter']));

        // sort
        if (isset($p_params["iSortCol_0"])) {
            $cols = array_keys($p_cols);
            $sortId = $p_params["iSortCol_0"];
            $sortBy = $cols[$sortId];
            $dir = $p_params["sSortDir_0"] ?: 'asc';
            switch($sortBy)
            {
                case 'commenter':
                    $qb->orderBy("c.name", $dir);
                    break;
                case 'thread':
                    $qb->orderBy("a.name", $dir);
                    break;
                case 'threadorder':
                    $qb->orderBy("e.thread_order", $dir);
                    break;
                case 'comment':
                case 'index':
                    $qb->orderBy("e.time_created", $dir);
                    break;
                default:
                    $qb->orderBy("e.".$sortBy, $dir);
            }
        }

        // limit
        if(isset($p_params['iDisplayLength']))
            $qb->setFirstResult((int) $p_params['iDisplayStart'])
               ->setMaxResults((int) $p_params['iDisplayLength']);
        return $qb->getQuery()->getResult();
    }

    /**
     * Get entity count
     *
     * @param array $p_params|null
     * @param array $p_cols|null
     *
     * @return int
     */
    public function getCount(array $p_params = null, array $p_cols = array())
    {
        $qb = $this->createQueryBuilder('e')
              ->leftJoin('e.commenter','c')
              ->leftJoin('e.thread','a')
              ->select('COUNT(e)');
        if(is_array($p_params) && !empty($p_params['sSearch']))
            $qb->where($this->buildWhere($p_cols, $p_params['sSearch'], $qb));

        if (is_array($p_params) && !empty($p_params['sFilter']))
            $qb->where($this->buildFilter($p_cols, $p_params['sFilter']));
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Build where condition
     *
     * @param array $cols
     * @param string $search
     * @return Doctrine\ORM\Query\Expr
     */
    protected function buildWhere(array $p_cols, $p_search, $qb)
    {
        $or = $qb->expr()->orx();
        $or->add($qb->expr()->like("c.name", $qb->expr()->literal("%{$p_search}%")));
        $or->add($qb->expr()->like("a.name", $qb->expr()->literal("%{$p_search}%")));
        $or->add($qb->expr()->like("e.subject", $qb->expr()->literal("%{$p_search}%")));
        $or->add($qb->expr()->like("e.message", $qb->expr()->literal("%{$p_search}%")));
        return $or;
    }

    /**
     * Build filter condition
     *
     * @param array $p_
     * @param string $p_cols
     * @return Doctrine\ORM\Query\Expr
     */
    protected function buildFilter(array $p_cols, array $p_filter)
    {
        $qb = $this->createQueryBuilder('e');
        $and = $qb->expr()->andx();
        foreach($p_filter as $key => $values)
        {
            if(!is_array($values))
                $values = array($values);
            $or = $qb->expr()->orx();
            switch($key)
            {
                case 'status':
                    $mapper = array_flip(Comment::$status_enum);
                    foreach($values as $value)
                        $or->add($qb->expr()->eq('e.status', $mapper[$value]));
                    break;
                case 'id':
                case 'forum':
                case 'thread':
                case 'language':
                    foreach($values as $value)
                        $or->add($qb->expr()->eq("e.$key", $value));
                    break;
            }
            $and->add($or);
        }
        return $and;
    }

    /**
     *
     * Delete article comments
     *
     * @param Newscoop\Entity\Article $p_article
     * @param Newscoop\Entity\Language $p_language
     */
    public function deleteArticle($p_article, $p_language = null)
    {
        $em = $this->getEntityManager();
        $params = array( 'thread' => $p_article );
        if(!is_null($p_language))
            $params['language'] = $p_language;
        $comments = $this->findBy($params);
        foreach($comments as $comment)
            $this->setCommentStatus($comment,'deleted');
    }

    /**
     *
     * Delete commenter commnets
     *
     * @param Newscoop\Entity\Commenter $p_commenter
     */
    public function deleteCommenter($p_commenter)
    {
        $em = $this->getEntityManager();
        $comments = $this->findByCommenter($p_commenter->getId());
        foreach($comments as $comment) {
            $this->setCommentStatus($comment,'deleted');
        }
    }

    /**
     *
     * Delete commenter commnets
     *
     * @param array $p_commenters
     */
    public function deleteCommenters(array $p_commenters)
    {
        $em = $this->getEntityManager();
        foreach($p_commenters as $commenter)
        {
            $comments = $this->findByCommenter($p_commenter->getId());
            foreach($comments as $comment) {
                $this->setCommentStatus($comment,'deleted');
            }
        }
    }

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

}
