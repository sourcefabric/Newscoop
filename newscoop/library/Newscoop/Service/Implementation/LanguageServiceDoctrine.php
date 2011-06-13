<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Implementation;

use Newscoop\Service\ILanguageService;
use Newscoop\Service\Model\Search\Search;
use Newscoop\Service\Model\Search\Column;
use Newscoop\Service\Model\SearchLanguage;
use Newscoop\Entity\Language;


/**
 * Provides the services implementation for the themes.
 */
class LanguageServiceDoctrine extends AEntityServiceDoctrine implements ILanguageService
{

	protected function _init_()
	{
		$this->entityClassName = Language::NAME;
		$this->searchClassName = SearchLanguage::NAME;
	}

	/* --------------------------------------------------------------- */

	protected function map(Search $search, Column $column)
	{
		return $this->_map($search, $column);
	}

	protected function _map(SearchLanguage $s, Column $col)
	{
		if($s->NAME === $col){
			return 'name';
		}
		throw new \Exception("Unknown column provided.");
	}

}