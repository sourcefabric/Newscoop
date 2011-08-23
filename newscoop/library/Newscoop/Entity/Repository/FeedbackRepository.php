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
        
        $subscriber = $em->getReference('Newscoop\Entity\User\Subscriber', $p_values['subscriber']);
        
        $p_entity->setSubscriber($subscriber);
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
		$qb = $this->createQueryBuilder('f');
        $qb->from('Newscoop\Entity\User\Subscriber', 's');
        $andx = $qb->expr()->andx();
        $andx->add($qb->expr()->eq('f.subscriber', new Expr\Literal('s.id')));

		if (!empty($p_params['sSearch'])) {
            $this->buildWhere($p_cols, $p_params['sSearch'], $qb, $andx);
        }
        
        // sort
        if (isset($p_params["iSortCol_0"])) {
            $cols = array_keys($p_cols);
            $sortId = $p_params["iSortCol_0"];
            $sortBy = $cols[$sortId];
            $dir = $p_params["sSortDir_0"] ? : 'asc';
            switch ($sortBy) {
                case 'user':
                    $qb->orderBy("s.name", $dir);
                    break;
                case 'message':
                    $qb->orderBy("f.message", $dir);
                    break;
                case 'url':
                    $qb->orderBy("f.url", $dir);
                    break;
                case 'index':
                    $qb->orderBy("f.time_created", $dir);
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
        
        $result = $qb->getQuery()->getResult();
        return $result;
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
        $orx->add($qb->expr()->like("s.name", $qb->expr()->literal("%{$p_search}%")));
        $orx->add($qb->expr()->like("f.message", $qb->expr()->literal("%{$p_search}%")));
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
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
