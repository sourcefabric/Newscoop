<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Service\IPublicationService;
use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Model\Search\Column;
use Newscoop\Service\Model\SearchPublication;
use Newscoop\Entity\Publication;

/**
 * Provides the services implementation for the Publications.
 */
class PublicationServiceDoctrine extends AEntityServiceDoctrine implements IPublicationService
{

	protected function _init_(){
		$this->entityClassName = Publication::NAME;
		$this->searchClassName = SearchPublication::NAME;
	}

	/* --------------------------------------------------------------- */

	protected function map(Search $search, Column $column)
	{
		return $this->_map($search, $column);
	}

	protected function _map(SearchPublication $s, Column $col)
	{
		if($s->NAME === $col){
			return 'name';
		}
		throw new \Exception("Unknown column provided.");
	}

}