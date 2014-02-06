<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Entity\Comment;
use Newscoop\Entity\Comment\Commenter;
use Newscoop\Datatable\Source as DatatableSource;
use Newscoop\Entity\User;
use Newscoop\Search\RepositoryInterface;

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
     * @param  int $article  Article number
     * @param  string $language Language code in format "en" for example.
     * @return Doctrine\ORM\Query           Query
     */
    public function getArticleComments($article, $language)
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

        $query = $queryBuilder->getQuery();

        return $query;
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
     * @param int $p_article
     * @param string $p_status
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
     * @param \Newscoop\Entity\Comment $p_comment
     * @param  string $p_status
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
    }

    /**
     * Method for recommending a comment
     * @param \Newscoop\Entity\Comment $p_comment
     * @return void
     */
    public function setRecommended(array $p_comment_ids, $p_recommended)
    {
        foreach ($p_comment_ids as $comment_id) {
			$this->setCommentRecommended($this->find($comment_id), $p_recommended);
		}
    }

    /**
     * Method for setting recommended for a comment
     * @param \Newscoop\Entity\Comment $p_comment
     * @param  string $p_recommended
     * @return void
     */
    public function setCommentRecommended(Comment $p_comment, $p_recommended)
    {
        $em = $this->getEntityManager();
        $p_comment->setRecommended($p_recommended);
        $em->persist($p_comment);
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
        $p_entity->setSubject($p_values['subject'])->setMessage($p_values['message'])->setTimeUpdated(new \DateTime);
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
        $p_values += array('recommended' => false);

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
				 ->setTimeCreated($p_values['time_created'])
                 ->setRecommended($p_values['recommended']);

        if (array_key_exists('source', $p_values)) {
            $p_entity->setSource($p_values['source']);
        }

        $threadLevel = 0;

        if (!empty($p_values['parent']) && (0 != $p_values['parent'])) {
            $parent = $this->find($p_values['parent']);
            // set parent of the comment
            $p_entity->setParent($parent)->setLanguage($parent->getLanguage())->setForum($parent->getForum())->setThread($parent->getThread());
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
            if (!isset($p_values['language'])) {
                $language = $thread->getLanguage();
            } else {
                $languageRepository = $em->getRepository('Newscoop\Entity\Language');
                $language = $languageRepository->find($p_values['language']);
            }

            $articleRepository = $em->getRepository('Newscoop\Entity\Article');
            $thread = $articleRepository->find(array('number' => $p_values['thread'], 'language' => $language->getId()));

            $query = $this->createQueryBuilder('c')
                ->select('MAX(c.thread_order)')
                ->where('c.thread = :thread')
                ->andWhere('c.language = :language')
                ->setParameter('thread', $thread->getNumber())
                ->setParameter('language', $language->getId())
                ->getQuery();

            // increase by one of the current comment
            $threadOrder = $query->getSingleScalarResult() + 1;

            $p_entity->setLanguage($language)->setForum($thread->getPublication())->setThread($thread);
        }

        $p_entity->setThreadOrder($threadOrder)->setThreadLevel($threadLevel);
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
            $qb->setFirstResult((int)$p_params['iDisplayStart'])->setMaxResults((int)$p_params['iDisplayLength']);
        }
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
     * @param array $cols
     * @param string $search
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
     *
     * @param array $p_
     * @param string $p_cols
     * @param
     * @return Doctrine\ORM\Query\Expr
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
     *
     * Delete article comments
     *
     * @param Newscoop\Entity\Article $p_article
     * @param Newscoop\Entity\Language $p_language
     */
    public function deleteArticle($p_article, $p_language = null)
    {
        $em = $this->getEntityManager();
        $params = array('thread' => $p_article);
        if (!is_null($p_language)) {
            $params['language'] = $p_language;
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
     *
     * Delete commenter commnets
     *
     * @param Newscoop\Entity\Commenter $p_commenter
     */
    public function deleteCommenter($p_commenter, $p_values)
    {
        $em = $this->getEntityManager();
        $comments = $this->findByCommenter($p_commenter->getId());
        foreach ($comments as $comment) {
            $this->setCommentStatus($comment, 'deleted');
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
        foreach ($p_commenters as $commenter) {
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
     * @param $p_comment_id
     */
    public function getDirectReplies($p_comment_id)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->add('select', 'c.id')
            ->add('from', 'Newscoop\Entity\Comment c')
            ->add('where', 'c.parent = :p_comment_id')
            ->setParameter('p_comment_id', $p_comment_id);
        $query = $qb->getQuery();
        $commentIds = $query->getArrayResult();

        $clearCommentIds = array();
        foreach($commentIds as $key => $value) {
        	$clearCommentIds[] = $value['id'];
        }
        return $clearCommentIds;
    }

    /**
     * Get comments count for user
     *
     * @param Newscoop\Entity\User $user
     * @return int
     */
    public function countByUser(User $user)
    {
        return (int) $this->getEntityManager()
            ->createQuery("SELECT COUNT(comment) FROM Newscoop\Entity\Comment comment WHERE comment.commenter IN (SELECT commenter.id FROM Newscoop\Entity\Comment\Commenter commenter WHERE commenter.user = :user)")
            ->setParameter('user', $user->getId())
            ->getSingleScalarResult();
    }

    /**
     * Find comments for indexing
     *
     * @param mixed $count Number of comments to index. When null default will be used
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
            die('Not implemented yet!');
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
     * @param array $comments
     * @return void
     */
    public function setIndexedNow(array $comments)
    {
        if (empty($comments)) {
            return;
        }

        $this->getEntityManager()->createQuery('UPDATE Newscoop\Entity\Comment c SET c.indexed = CURRENT_TIMESTAMP() WHERE c.id IN (:comments)')
            ->setParameter('comments', array_map(function($comment) { return $comment->getId(); }, $comments))
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
}
