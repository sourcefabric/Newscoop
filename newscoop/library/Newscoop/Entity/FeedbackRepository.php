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
use Newscoop\Entity\Feedback;
use Newscoop\Entity\User;
use Newscoop\Datatable\Source as DatatableSource;

/**
 * Feedback repository
 */
class FeedbackRepository extends DatatableSource
{

    /**
     * Get new instance of the comment
     *
     * @return \Newscoop\Entity\Feedback
     */
    public function getPrototype()
    {
        return new Feedback;
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
        foreach ($p_comment_ids as $comment_id) $this->setCommentStatus($this->find($comment_id), $p_status);
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
     * Method for update a comment
     *
     * @param Comment $p_entity
     * @param array $params
     * @return Comment $p_entity
     */
    public function update(Comment $p_entity, $p_values)
    {
        // get the entity manager
        $em = $this->getEntityManager();
        $p_entity->setSubject($p_values['subject'])->setMessage($p_values['message'])->setTimeUpdated(new \DateTime);
        $em->persist($p_entity);
        return $p_entity;
    }

    /**
     * Method for saving a feedback
     *
     * @param Feedback $p_entity
     * @param array $params
     * @return Feedback $p_entity
     */
    public function save(Feedback $p_entity, $p_values)
    {
		// get the entity manager
        $em = $this->getEntityManager();
        
        $p_entity->setSubscriber($em->getReference('Newscoop\Entity\User\Subscriber', $p_values['subscriber']));
        $p_entity->setMessage($p_values['message']);
        $p_entity->setUrl($p_values['url']);
        $p_entity->setTimeCreated($p_values['time_created']);

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
    protected function buildWhere(array $p_cols, $p_search, $qb, $andx)
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
        foreach ($comments as $comment) $this->setCommentStatus($comment, 'deleted');
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
     *
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

}
