<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Utils\Validation;
use Newscoop\Service\Model\Search\Column;
use Newscoop\Service\Model\Search\Search;
use Newscoop\Entity\Resource;
use Newscoop\Service\Model\SearchResource;
use Newscoop\Service\ISyncResourceService;


/**
 * Provides the services implementation for the Outputs.
 */
class SyncResourceServiceDoctrine extends AEntityServiceDoctrine implements ISyncResourceService
{

	protected function _init_(){
		$this->entityClassName = Resource::NAME;
		$this->searchClassName = SearchResource::NAME;
	}

	/* --------------------------------------------------------------- */

	function getSynchronized(Resource $resource)
	{
		Validation::notEmpty($resource, 'resource');
		Validation::notEmpty($resource->getPath(), 'resource.path');

		$em = $this->getEntityManager();
		if($resource->getId() === NULL){
			$resources = $em->getRepository($this->entityClassName)->findByPath($resource->getPath());
			if(isset($resources) && count($resources) > 0){
				return $resources[0];
			}
			$em->persist($resource);
			$em->flush();
			return $resource;
		}
		$resources = $em->getRepository($this->entityClassName)->findByName($name);
		if(isset($resources) && count($resources) > 0){
			return $resources[0];
		}
		return NULL;
	}

	/* --------------------------------------------------------------- */

	protected function map(Search $search, Column $column)
	{
		return $this->_map($search, $column);
	}

	protected function _map(SearchResource $s, Column $col)
	{
		if($s->NAME === $col){
			return 'name';
		}
		if($s->PATH === $col){
			return 'path';
		}
		throw new \Exception("Unknown column provided.");
	}

}