<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Newscoop\Service\IEntityService;
use Newscoop\Service\Model\Search\ColumnOrder;
use Newscoop\Service\Model\Search\ColumnOrderLike;
use Newscoop\Utils\Validation;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\Model\Search\Column;
use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Implementation\AEntityBaseServiceDoctrine;

/**
 * Provides the services implementation for the themes.
 */
abstract class AEntityServiceDoctrine extends AEntityBaseServiceDoctrine implements IEntityService
{

	/** @var string */
	protected $searchClassName;

	/* ------------------------------- */

	/**
	 * Construct the service base d on the provided resource id.
	 * @param ResourceId $id
	 * 		The resource id, not null not empty
	 */
	function __construct(ResourceId $id)
	{
            parent::__construct($id);
            if(is_null($this->searchClassName)){
			throw \Exception("Please provide a search class name to be used.");
		}
	}

	/* --------------------------------------------------------------- */


	function getCount(Search $search = NULL)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();

		$qb->select('COUNT('.self::ALIAS.')')->from($this->entityClassName, self::ALIAS);

		if($search !== NULL){
			if(get_class($search) !== $this->searchClassName){
				throw new \Exception("The search needs to be a '.$this->searchClassName.' instance.");
			}
			$this->processInterogation($search, $qb);
		}

		$result = $qb->getQuery()->getResult();
		return (int) $result[0][1];
	}

	function getEntities(Search $search = NULL, $offset = 0, $limit = -1)
	{
		$qb = $this->getEntityManager()->createQueryBuilder();

		$qb->select(self::ALIAS)->from($this->entityClassName, self::ALIAS);

		if($search !== NULL){
			if(get_class($search) !== $this->searchClassName){
				throw new \Exception("The search needs to be a '.$this->searchClassName.' instance.");
			}
			$this->processInterogation($search, $qb);
			$this->processOrder($search, $qb);
		}

		if($limit >= 0){
			$qb->setFirstResult($offset);
			$qb->setMaxResults($limit);
		}

		return $qb->getQuery()->getResult();
	}

	/* --------------------------------------------------------------- */


	/**
	 * Builds on to the provided query builder the interogations that will reflect the provided search object.
	 *
	 * @param Search $search
	 * 		The search from which the query is build, if the search does not reflect any interogation
	 * 		no actions needs to be taken, *(not null not empty).
	 *
	 * @param QueryBuilder $qb
	 * 		The Doctrine query builder to be constructed on, *(not null not empty).
	 */
	protected function processInterogation(Search $search, QueryBuilder $qb){
		foreach ($search->getAllColumns() as $column){
			/** @var $column Newscoop\Service\Model\Search\ColumnOrderLike */
			if($column instanceof ColumnOrderLike){
				$like = $column->getLike();
				if(!empty($like)){
					$name = $this->map($search, $column);
					$field = self::ALIAS.'.'.$name;

					$qb->andWhere($field.' like :'.$name);
					$qb->setParameter($name, $like);
				}
			}
		}
	}

	/**
	 *  Builds on to the provided query builder the ordering that will reflect the provided search object.
	 *
	 * @param Search $search
	 * 		The search from which the query is build, if the search does not reflect any ordering
	 * 		no actions needs to be taken, *(not null not empty).
	 *
	 * @param QueryBuilder $qb
	 * 		The Doctrine query builder to be constructed on, *(not null not empty).
	 */
	protected function processOrder(Search $search, QueryBuilder $qb){
		foreach ($search->getOrderedBy() as $column){
			/** @var $column Newscoop\Service\Model\Search\ColumnOrder */
			if($column instanceof ColumnOrder){
				$field = self::ALIAS.'.'.$this->map($search, $column);
				if($column->isOrderAscending() === TRUE){
					$order = 'ASC';
				} else {
					$order = 'DESC';
				}

				$qb->addOrderBy($field, $order);
			}
		}
	}

	/* --------------------------------------------------------------- */

	/**
	 * Maps a search column to an actual doctrine field name.
	 *
	 * @param Search $search
	 * 		The search object containing the column, *(not null not empty).
	 *
	 * @param Column $column
	 * 		The column for which to get the doctrine field name, *(not null not empty).
	 *
	 * @return string
	 * 		The doctrine field name.
	 */
	protected abstract function map(Search $search, Column $column);
}