<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service\Model\Search;

use Newscoop\Service\Model\Search\ColumnOrder;

/**
 * Provides the column data container, used as a reference for comparation.
 */
class ColumnOrderLike extends ColumnOrder
{

	/** @var string **/
	private $like = NULL;

	/* --------------------------------------------------------------- */

	/**
	 * Set the search criteria on this column to be like the provided text, the text needs to have specified the place holders
	 * ex: '%right%' or '%rat%tuil'.
	 *
	 * @param string $like
	 *		The text to be used for the like comparison, if null or empty will
	 *		reset the like search option.
	 *
	 * @return Newscoop\Service\Model\Search\ColumnOrderLike
	 *		This instance, used for chaining purposes.
	 */
	public function setLike($like)
	{
		$this->like = $like;
		return $this;
	}

	/**
	 * Provides the search criteria on this column to be like the provided text.
	 *
	 * @return string
	 *		The text to be used for the like comparison, if null or empty will
	 *		reset the like search option.
	 */
	public function getLike(){
		return $this->like;
	}
}