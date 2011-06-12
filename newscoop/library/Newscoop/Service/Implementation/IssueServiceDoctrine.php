<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Service\IIssueService;
use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Model\Search\Column;
use Newscoop\Service\Model\SearchIssue;
use Newscoop\Entity\Issue;

/**
 * Provides the services implementation for the Issues.
 */
class IssueServiceDoctrine extends AEntityServiceDoctrine implements IIssueService
{

	protected function _init_(){
		$this->entityClassName = Issue::NAME;
		$this->searchClassName = SearchIssue::NAME;
	}

	/* --------------------------------------------------------------- */

	protected function map(Search $search, Column $column)
	{
		return $this->_map($search, $column);
	}

	protected function _map(SearchIssue $serch, Column $column)
	{
		if($s->NAME === $col){
			return 'name';
		}
		throw new \Exception("Unknown column provided.");
	}

}