<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Newscoop\Entity\Comment;
use Newscoop\Entity\Comment\Commenter;
use Newscoop\Datatable\Source as DatatableSource;
use Newscoop\Entity\User;
use Newscoop\Search\RepositoryInterface;
use Newscoop\NewscoopException\IndexException;

/**
 * Comment repository
 */
class CommentRepository extends DatatableSource implements RepositoryInterface
{

    /**
     * Get new instance of the comment
     *
     * @return \Newscoop\Entity\Comment
     */
    public function getPrototype()
    {
        return new Comment;
    }

    /**
     * Get comments for article
     *
     * @param int    $article  Article number
     * @param string $language Language code in format "en" for example.
     *
     * @return Doctrine\ORM\Query Query
     */
    public function getArticleComments($article, $language, $recommended = false, $getDeleted = true)
    {
        $em = $this->getEntityManager();
        $languageId = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language);

        $queryBuilder = $em->getRepository('Newscoop\Entity\Comment')
            ->createQueryBuilder('c')
            ->where('c.thread = :thread')
            ->andWhere('c.language = :language')
            ->setParameters(array(
                'thread' => $article,
                'language' => $languageId->getId()
            ));
        if ($recommended) {
            $queryBuilder->andWhere('c.recommended = 1');
        }

        if (!$getDeleted) {
            $queryBuilder->andWhere('c.status != :status')
                ->setParameter('status', Comment::STATUS_DELETED);
        }

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get all comments query
     *
     * @return Query
     */
    public function getComments($getDeleted = true)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Comment')
            ->createQueryBuilder('c');

        if (!$getDeleted) {
            $queryBuilder->andWhere('c.status != :status')
                ->setParameter('status', Comment::STATUS_DELETED);
        }

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get single comment query
     *
     * @param int $id
     *
     * @return Query
     */
    public function getComment($id, $getDeleted = true)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Comment')
            ->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id);

        if (!$getDeleted) {
            $queryBuilder->andWhere('c.status != :status')
                ->setParameter('status', Comment::STATUS_DELETED);
        }

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Method for setting status
     *
     * @param  array  $p_comment_ids
     * @param  string $p_status
     * @return void
     */
    public function setStatus(array $p_comment_ids, $p_status)
    {
        $comments = array();
        foreach (array_unique($p_comment_ids) as $comment_id) {
            $one_comment = $this->find($comment_id);
            if (!empty($one_comment)) {
                $comments[] = $one_comment;
            }
        }

        if ('deleted' == $p_status) {
            foreach ($comments as $one_comment) {
                $one_comment->setParent();
            }
        }

        foreach ($comments as $one_comment) {
            $this->setCommentStatus($one_comment, $p_status);
        }
    }

    /**
     * Method for setting status per article
     *
     * @param  int    $p_article
     * @param  string $p_status
     * @return void
     */
    public function setArticleStatus($p_article, $p_language, $p_status)
    {
        $em = $this->getEntityManager();
        $params = array('thread' => $p_article, 'language' => $p_language);
        if ($p_status == 'hidden') {
            $params['status'] = 0;
        } elseif ($p_status == 'approved') {
            $params['status'] = 2;
        }
        $comments = $this->findBy($params);
        foreach ($comments as $comment) {
            $this->setCommentStatus($comment, $p_status);
        }

    }

    /**
     * Method for setting status for a comment
     * @param  \Newscoop\Entity\Comment $p_comment
     * @param  string                   $p_status
     * @return void
     */
    private function setCommentStatus(Comment $p_comment, $p_status)
    {
        $em = $this->getEntityManager();
        if ($p_status == 'deleted') {
            $em->remove($p_comment);
        } else {
            $p_comment->setStatus($p_status);
            $em->persist($p_comment);
        }

        $em->flush();

        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheService->clearNamespace('comment');

        $user = $p_comment->getCommenter()->getUser();

        if ($user instanceof User) {
            $em->getRepository('Newscoop\Entity\User')->setUserPoints($user);
        }
    }

    /**
     * Method for recommending a comment
     *
     * @param array   $commentIds
     * @param integer $recommended
     *
     * @return void
     */
    public function setRecommended(array $commentIds, $recommended)
    {
        foreach ($commentIds as $commentId) {
            $this->setCommentRecommended($this->find($commentId), $recommended);
        }
    }

    /**
     * Method for setting recommended for a comment
     *
     * @param \Newscoop\Entity\Comment $comment
     * @param string                   $recommended
     *
     * @return void
     */
    public function setCommentRecommended(Comment $comment, $recommended)
    {
        $em = $this->getEntityManager();
        $comment->setRecommended($recommended);
        $em->persist($comment);
    }

    /**
     * Method for update a comment
     *
     * @param array   $values
     *
     * @return Comment $enitity
     */
    public function update(Comment $comment, $values)
    {
        // get the enitity manager
        $em = $this->getEntityManager();
        if (array_key_exists('subject', $values) && !is_null($values['subject'])) {
            $comment->setSubject($values['subject']);
        }
        if (array_key_exists('message', $values) && !is_null($values['message'])) {
            $comment->setMessage($values['message']);
        }
        if (array_key_exists('recommended', $values) && !is_null($values['recommended'])) {
            $comment->setRecommended($values['recommended']);
        }
        if (array_key_exists('status', $values) && !is_null($values['status'])) {
            $comment->setStatus($values['status']);
        }
        $comment->setTimeUpdated(new \DateTime());

        return $comment;
    }

    /**
     * Method for saving a comment
     *
     * @param Comment $entity
     * @param array   $values
     *
     * @return Comment
     */
    public function save(Comment $entity, $values)
    {
        $values += array('recommended' => false);
        $em = $this->getEntityManager();

        $commenterRepository = $em->getRepository('Newscoop\Entity\Comment\Commenter');

        $commenter = new Commenter();
        $commenter = $commenterRepository->save($commenter, $values);

        $entity->setCommenter($commenter)
            ->setSubject($values['subject'])
            ->setMessage($values['message'])
            ->setStatus($values['status'])
            ->setIp($values['ip'])
            ->setTimeCreated($values['time_created'])
            ->setRecommended($values['recommended']);

        if (array_key_exists('source', $values)) {
            $entity->setSource($values['source']);
        }

        $threadLevel = 0;

        if (!empty($values['parent']) && (0 != $values['parent'])) {
            $parent = $this->find($values['parent']);
            // set parent of the comment
            $entity
                ->setParent($parent)
                ->setLanguage($parent->getLanguage())
                ->setForum($parent->getForum())
                ->setThread($parent->getThread());
            /**
             * get the maximum thread order from the current parent
             */
            $qb = $this->createQueryBuilder('c');
            $threadOrder =
            $qb->select('MAX(c.thread_order)')
                    ->andwhere('c.parent = :parent')
                    ->andWhere('c.thread = :thread')
                    ->andWhere('c.language = :language')
                    ->setParameter('parent', $parent)
                    ->setParameter('thread', $parent->getThread()->getId())
                    ->setParameter('language', $parent->getLanguage()->getId());

            $threadOrder = $threadOrder->getQuery()->getSingleScalarResult();
            // if the comment parent doesn't have children then use the parent thread order
            if (empty($threadOrder)) {
                $threadOrder = $parent->getThreadOrder();
            }
            $threadOrder += 1;
            /**
             * update all the comment for the thread where thread order is less or equal
             * of the current thread_order
             */
            $qb = $this->createQueryBuilder('c');
            $qb->update()
               ->set('c.thread_order',  'c.thread_order+1')
               ->andwhere('c.thread_order >= :thread_order')
               ->andWhere('c.thread = :thread')
               ->andWhere('c.language = :language')
                    ->setParameter('language', $parent->getLanguage()->getId())
                    ->setParameter('thread', $parent->getThread()->getId())
                    ->setParameter('thread_order', $threadOrder);
            $qb->getQuery()->execute();
            // set the thread level the thread level of the parent plus one the current level
            $threadLevel = $parent->getThreadLevel() + 1;
        } else {
            $languageRepository = $em->getRepository('Newscoop\Entity\Language');

            if (is_numeric($values['language'])) {
                $language = $languageRepository->findOneById($values['language']);
            } else {
                $language = $languageRepository->findOneByCode($values['language']);
            }

            $articleRepository = $em->getRepository('Newscoop\Entity\Article');
            $thread = $articleRepository->find(array('number' => $values['thread'], 'language' => $language->getId()));

            $query = $this->createQueryBuilder('c')
                ->select('MAX(c.thread_order)')
                ->where('c.thread = :thread')
                ->andWhere('c.language = :language')
                ->setParameter('thread', $thread->getNumber())
                ->setParameter('language', $language->getId())
                ->getQuery();

            // increase by one of the current comment
            $threadOrder = $query->getSingleScalarResult() + 1;

            $entity
                ->setLanguage($language)
                ->setForum($thread->getPublication())
                ->setThread($thread);
        }

        $entity->setThreadOrder($threadOrder)->setThreadLevel($threadLevel);
        $em->persist($entity);

        $user = $commenter->getUser();
        if ($user instanceof User) {
            $em->getRepository('Newscoop\Entity\User')->setUserPoints($user);
        }

        return $entity;
    }

    /**
     * Get data for table
     *
     * @param  array     $p_params
     * @param  array     $p_cols
     * @return Comment[]
     */
    public function getData(array $p_params, array $p_cols)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->from('Newscoop\Entity\Comment\Commenter', 'c')
                ->from('Newscoop\Entity\Article', 'a');
        $andx = $qb->expr()->andx();
        $andx->add($qb->expr()->eq('e.language', new Expr\Literal('a.language')));
        $andx->add($qb->expr()->eq('e.thread', new Expr\Literal('a.number')));
        $andx->add($qb->expr()->eq('e.commenter', new Expr\Literal('c.id')));

        if (!empty($p_params['sSearch'])) {
            $this->buildWhere($p_cols, $p_params['sSearch'], $qb, $andx);
        }

        if (!empty($p_params['sFilter'])) {
            $this->buildFilter($p_cols, $p_params['sFilter'], $qb, $andx);
        }

        // sort
        if (isset($p_params["iSortCol_0"])) {
            $cols = array_keys($p_cols);
            $sortId = $p_params["iSortCol_0"];
            $sortBy = $cols[$sortId];
            $dir = $p_params["sSortDir_0"] ? : 'asc';
            switch ($sortBy) {
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
                    $qb->orderBy("e." . $sortBy, $dir);
            }
        }
        $qb->where($andx);
        // limit
        if (isset($p_params['iDisplayLength'])) {
            $qb->setFirstResult((int) $p_params['iDisplayStart'])->setMaxResults((int) $p_params['iDisplayLength']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Get entity count
     *
     * @param array $p_params
     * @param array $p_cols
     *
     * @return int
     */
    public function getCount(array $p_params = null, array $p_cols = array())
    {
        $qb = $this->createQueryBuilder('e');
        $qb->from('Newscoop\Entity\Comment\Commenter', 'c')
                ->from('Newscoop\Entity\Article', 'a');
        $andx = $qb->expr()->andx();
        $andx->add($qb->expr()->eq('e.language', new Expr\Literal('a.language')));
        $andx->add($qb->expr()->eq('e.thread', new Expr\Literal('a.number')));
        $andx->add($qb->expr()->eq('e.commenter', new Expr\Literal('c.id')));

        if (is_array($p_params) && !empty($p_params['sSearch'])) {
            $this->buildWhere($p_cols, $p_params['sSearch'], $qb, $andx);
        }

        if (is_array($p_params) && !empty($p_params['sFilter'])) {
            $this->buildFilter($p_cols, $p_params['sFilter'], $qb, $andx);
        }

        $qb->where($andx);
        $qb->select('COUNT(e)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Build where condition
     *
     * @param  array                   $p_cols
     * @param  string                  $p_search
     * @return Doctrine\ORM\Query\Expr
     */
    protected function buildWhere(array $p_cols, $p_search, $qb = null, $andx = null)
    {
        $orx = $qb->expr()->orx();
        $orx->add($qb->expr()->like("c.name", $qb->expr()->literal("%{$p_search}%")));
        $orx->add($qb->expr()->like("a.name", $qb->expr()->literal("%{$p_search}%")));
        $orx->add($qb->expr()->like("e.subject", $qb->expr()->literal("%{$p_search}%")));
        $orx->add($qb->expr()->like("e.message", $qb->expr()->literal("%{$p_search}%")));

        return $andx->add($orx);
    }

    /**
     * Build filter condition
     */
    protected function buildFilter(array $p_cols, array $p_filter, $qb, $andx)
    {
        foreach ($p_filter as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            $orx = $qb->expr()->orx();
            switch ($key) {
                case 'status':
                    $mapper = array_flip(Comment::$status_enum);
                    foreach ($values as $value) {
                        $orx->add($qb->expr()->eq('e.status', $mapper[$value]));
                    }
                    break;
                case 'id':
                case 'forum':
                case 'thread':
                case 'language':
                    foreach ($values as $value) {
                        $orx->add($qb->expr()->eq("e.$key", $value));
                    }
                    break;
                case 'recommended':
                    foreach ($values as $value) {
                        $orx->add($qb->expr()->eq('e.recommended', $value));
                    }
            }
            $andx->add($orx);
        }

        return $andx;
    }

    /**
     * Delete article comments
     *
     * @param Newscoop\Entity\Article  $article
     * @param Newscoop\Entity\Language $language
     */
    public function deleteArticle($article, $language = null)
    {
        $em = $this->getEntityManager();
        $params = array('thread' => $article);
        if (!is_null($language)) {
            $params['language'] = $language;
        }
        $comments = $this->findBy($params);
        foreach ($comments as $comment) {
            $comment->setParent();
        }
        foreach ($comments as $comment) {
            $this->setCommentStatus($comment, 'deleted');
        }
    }

    /**
     * Delete commenter comments
     *
     * @param Newscoop\Entity\Commenter $commenter
     */
    public function deleteCommenter($commenter)
    {
        $em = $this->getEntityManager();
        $comments = $this->findByCommenter($commenter->getId());
        foreach ($comments as $comment) {
            $this->setCommentStatus($comment, 'deleted');
        }
    }

    /**
     * Delete commenter comments
     *
     * @param array $commenters
     */
    public function deleteCommenters(array $commenters)
    {
        $em = $this->getEntityManager();
        foreach ($commenters as $commenter) {
            $comments = $this->findByCommenter($commenter->getId());
            foreach ($comments as $comment) {
                $this->setCommentStatus($comment, 'deleted');
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

    /**
     * Get direct replies to a comment
     *
     * @param $commentId
     *
     * return array
     */
    public function getDirectReplies($commentId)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->add('select', 'c.id')
            ->add('from', 'Newscoop\Entity\Comment c')
            ->add('where', 'c.parent = :p_comment_id')
            ->setParameter('p_comment_id', $commentId);
        $query = $qb->getQuery();
        $commentIds = $query->getArrayResult();

        $clearCommentIds = array();
        foreach ($commentIds as $key => $value) {
            $clearCommentIds[] = $value['id'];
        }

        return $clearCommentIds;
    }

    /**
     * Get comments count for user
     *
     * @param Newscoop\Entity\User $user
     *
     * @return int
     */
    public function countByUser(User $user)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $qb->select('commenter.id')
            ->from('Newscoop\Entity\Comment\Commenter', 'commenter')
            ->where('commenter.user = :commenterUserId')
            ->setParameter('commenterUserId', $user->getId());
        $commenterId = $qb->getQuery()->getArrayResult();

        if (array_key_exists(0, $commenterId)) {
            $commenterId = $commenterId[0];
        } else {
            return 0;
        }

        if (is_array($commenterId) && array_key_exists('id', $commenterId)) {
            $qb = $em->createQueryBuilder();
            $qb->select('count(comment.id)')
                ->from('Newscoop\Entity\Comment', 'comment')
                ->where('comment.commenter = :commenter')
                ->setParameter('commenter', $commenterId['id']);

            return (int) $qb->getQuery()->getSingleScalarResult();
        } else {
            return 0;
        }
    }

    /**
     * Find comments for indexing
     *
     * @param integer $count Number of comments to index. When null default will be used
     *
     * @return array
     */
    public function getBatch($count = self::BATCH_COUNT, array $filter = null)
    {
        $qb = $this->createQueryBuilder('c');

        if (is_null($filter)) {
            $qb->where('c.indexed IS NULL')
                ->orWhere('c.indexed < c.time_updated')
                ->orderBy('c.time_updated', 'DESC');
        } else {
            throw new IndexException("Filter is not implemented yet.");
        }

        if (is_numeric($count)) {
            $qb->setMaxResults($count);
        }

        $batch = $qb->getQuery()
            ->getResult();

        return $batch;
    }

    /**
     * Set indexed now
     *
     * @param  array $comments
     * @return void
     */
    public function setIndexedNow(array $comments)
    {
        if (empty($comments)) {
            return;
        }

        $this->getEntityManager()->createQuery('UPDATE Newscoop\Entity\Comment c SET c.indexed = CURRENT_TIMESTAMP() WHERE c.id IN (:comments)')
            ->setParameter('comments', array_map(function ($comment) { return $comment->getId(); }, $comments))
            ->execute();
    }

    /**
     * Set indexed null
     *
     * @return void
     */
    public function setIndexedNull(array $comments = null)
    {
        $this->getEntityManager()->createQuery('UPDATE Newscoop\Entity\Comment c SET c.indexed = NULL')
            ->execute();
    }

    /**
     * Get Comments for all authors articles grouped by day
     *
     * @param \Newscoop\Entity\Author   $author
     * @param string $range
     *
     * @return Doctrine\ORM\Query
     */
    public function getCommentsForAuthorArticlesPerDay($author, $range = '-60 days')
    {
        $qb = $this->createQueryBuilder('c');
        $date = new \DateTime();
        $date->modify($range);

        $qb->select('COUNT(c.id) as number', "DATE_FORMAT(c.time_created, '%Y-%m-%d') as date")
            ->leftJoin('c.article', 'a')
            ->leftJoin('a.authors', 'aa')
            ->andwhere('aa.id = :authorId')
            ->andWhere('c.time_created > :date')
            ->setParameter('authorId', $author->getId())
            ->setParameter('date', $date)
            ->groupBy('date');

        return $qb->getQuery();
    }
}
