<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Service\IOutputService;

use Newscoop\Entity\Output;
use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Model\Search\Column;
use Newscoop\Service\Model\SearchOutput;

/**
 * Provides the services implementation for the Outputs.
 */
class OutputServiceDoctrine extends AEntityServiceDoctrine implements IOutputService
{

	protected function _init_(){
		$this->entityClassName = Output::NAME;
		$this->searchClassName = SearchOutput::NAME;
	}

	/* --------------------------------------------------------------- */

	function findByName($name)
	{
		Validation::notEmpty($name, 'name');
		$em = $this->getEntityManager();
		return $em->getRepository($this->entityClassName)->findByName($name);
	}

	/* --------------------------------------------------------------- */

	protected function map(Search $search, Column $column)
	{
		return $this->_map($search, $column);
	}

	protected function _map(SearchOutput $s, Column $col)
	{
		if($s->NAME === $col){
			return 'name';
		}
		throw new \Exception("Unknown column provided.");
	}

}