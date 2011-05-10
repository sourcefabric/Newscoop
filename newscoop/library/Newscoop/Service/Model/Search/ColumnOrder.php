<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Model\Search;

/**
 * A Column extension that also provides the ability to order by.
 */
class ColumnOrder extends Column
{

	/** @var bool **/
	private $ascending = NULL;

	/* --------------------------------------------------------------- */
	
	/**
	 * Provides the ordering status of the column.
	 * @return NULL|bool
	 * 		Returns NULL if this column is not ordered, true if the order is ascending, flase for descending.
	 */
	public function isOrderAscending(){
		return $this->ascending;
	}
	
	/* --------------------------------------------------------------- */

	/**
	 * Set this column to be ordered.
	 *
	 * @param bool $ascending
	 *		If true than the column will be ordered asscending, if false descending.
	 *
	 * @return Newscoop\Service\Model\Search\Column
	 *		This instance, used for chaining purposes.
	 */
	public function order($ascending){
		if($this->ascending === NULL){
			// Added first time so we should also register this as a sor by column.
			$this->search->addOrderBy($this);
		}
		$this->ascending = $ascending;
		return $this;
	}

	/**
	 * Set this column to be ordered asscending.
	 *
	 * @return Newscoop\Service\Model\Search\Column
	 *		This instance, used for chaining purposes.
	 */
	public function orderAscending(){
		$this->order(TRUE);
		return $this;
	}

	/**
	 * Set this column to be ordered descending.
	 *
	 * @return Newscoop\Service\Model\Search\Column
	 *		This instance, used for chaining purposes.
	 */
	public function orderDescending(){
		$this->order(FALSE);
		return $this;
	}
}