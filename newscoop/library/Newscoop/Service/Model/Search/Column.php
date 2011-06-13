<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Model\Search;

use Newscoop\Utils\Validation;
use Newscoop\Service\Model\Search\Search;

/**
 * Provides the column data container.
 */
class Column
{

	/** @var Newscoop\Service\Model\Search\Search **/
	protected $search;

	/**
	 * Creates a new column.
	 *
	 * @param Newscoop\Service\Model\Search\Search $search
	 *		The search instance that is the owner of this Column.
	 */
	function __construct(Search $search)
	{
		Validation::notEmpty($search, 'search');
		$this->search = $search;
		$this->search->register($this);
	}

}