<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Model\Search;

use Newscoop\Utils\Validation;
use Newscoop\Service\Model\Search\Column;

/**
 * Provides the container for the search functionality.
 *
 */
class Search
{

	/** @var array **/
	private $allColumns = array();

	/** @var array **/
	private $orderBy = array();

	/* --------------------------------------------------------------- */

	/**
	 * (DO NOT CALL THIS DIRECTLY)
	 * Register the column to this search.
	 *
	 * @param Newscoop\Service\Model\Search\Column $column
	 *		The column to register, must not be null or empty.
	 *
	 * @return Newscoop\Service\Model\Search\Column
	 *		The same column provided, used for chaining purposes.
	 */
	public function register(Column $column)
	{
		Validation::notEmpty($column, 'column');

		$this->allColumns[] = $column;

		return $column;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides all the columns registered in this search.
	 *
	 * @return array
	 * 		The arrays containing all the Columns registered.
	 */
	public function getAllColumns()
	{
		return $this->allColumns;
	}

	/* --------------------------------------------------------------- */

	/**
	 * (DO NOT CALL THIS DIRECTLY)
	 * Add an order by column for this search.
	 *
	 * @param Newscoop\Service\Model\Search\Column $column
	 *		The column to order by, must not be null or empty.
	 */
	public function addOrderBy(Column $column)
	{
		Validation::notEmpty($column, 'column');
		$this->orderBy[] = $column;
	}

	/**
	 * Provides the columns to be ordered by in this search.
	 * The order in the array will be the order in which the columns have been specified for ordering.
	 *
	 * @return array
	 * 		The arrays containing the Columns to be ordered by.
	 */
	public function getOrderedBy()
	{
		return $this->orderBy;
	}

	/* --------------------------------------------------------------- */

}