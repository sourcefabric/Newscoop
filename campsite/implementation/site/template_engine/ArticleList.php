<?php

require_once('ListObject.php');


$iterator = new ArrayIterator();

/**
 * ArticleList class
 *
 */
class ArticleList extends ListObject
{
	/**
	 * Creates the list of objects. Sets the parameter $p_hasNextElements to
	 * true if this list is limited and elements still exist in the original
	 * list (from which this was truncated) after the last element of this
	 * list.
	 *
	 * @param int $p_start
	 * @param int $p_limit
	 * @param bool $p_hasNextElements
	 * @return array
	 */
	public function createList($p_start = 0, $p_limit = 0, &$p_hasNextElements)
	{
		$p_hasNextElements = false;
		return array();
	}

	/**
	 * Processes list constraints passed in a string.
	 *
	 * @param string $p_constraintsStr
	 * @return array
	 */
	public function processConstraints($p_constraintsStr)
	{
		return array();
	}

	/**
	 * Processes order constraints passed in a string.
	 *
	 * @param string $p_orderStr
	 * @return array
	 */
	public function processOrderString($p_orderStr)
	{
		return array();
	}
}

?>
