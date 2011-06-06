<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Service\ISectionService;
use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Model\Search\Column;
use Newscoop\Service\Model\SearchSection;
use Newscoop\Entity\Section;

/**
 * Provides the services implementation for the Issues.
 */
class SectionServiceDoctrine extends AEntityServiceDoctrine implements ISectionService
{

	protected function _init_(){
		$this->entityClassName = Section::NAME;
		$this->searchClassName = SearchSection::NAME;
	}

	/* --------------------------------------------------------------- */

	protected function map(Search $search, Column $column)
	{
		return $this->_map($search, $column);
	}

	protected function _map(SearchSection $serch, Column $column)
	{
		if($s->NAME === $col){
			return 'name';
		}
		throw new \Exception("Unknown column provided.");
	}

}