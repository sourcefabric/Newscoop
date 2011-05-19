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


/**
 * Provides the services implementation for the themes.
 */
abstract class AEntityServiceDoctrine implements IEntityService
{

	const ALIAS = 'en';

	/* --------------------------------------------------------------- */

	/** @var Newscoop\Service\Resource\ResourceId */
	private $id;
	/** @var Doctrine\ORM\EntityManager */
	private $em = NULL;

	/* ------------------------------- */

	/** @var string */
	protected $entityClassName;
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
		Validation::notEmpty($id, 'id');
		$this->id = $id;

		$this->_init_();

		if(is_null($this->entityClassName)){
			throw \Exception("Please provide a entitity class name to be used");
		}
		if(is_null($this->searchClassName)){
			throw \Exception("Please provide a search class name to be used.");
		}
	}

	/* --------------------------------------------------------------- */

	function getById($id)
	{
		Validation::notEmpty($id, 'id');
		$entity = $this->findById($id);
		if($entity === NULL){
			throw \Exception("Cannot locate '$this->entityClassName' for id '$id'.");
		}
		return $entity;
	}

	function findById($id)
	{
		Validation::notEmpty($id, 'id');
		$em = $this->getEntityManager();
		return $em->find($this->entityClassName, $id);
	}

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
	 * Provides the resource id.
	 *
	 * @return Newscoop\Services\Resource\ResourceId
	 *		The resource id.
	 */
	protected function getResourceId()
	{
		return $this->id;
	}

	/** Provides the dictrine entity manager.
	 *
	 * @return Doctrine\ORM\EntityManager
	 * 		The doctrine entity manager.
	 */
	protected function getEntityManager()
	{
		if($this->em === NULL){
			$doctrine = \Zend_Registry::get('doctrine');
			$this->em = $doctrine->getEntityManager();
		}
		return $this->em;
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
	 * Provides aditional initialization for the service.
	 */
	protected abstract function _init_();

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