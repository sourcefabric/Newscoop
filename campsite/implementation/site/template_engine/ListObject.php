<?php

//require_once('spl.php');
require_once('Operator.php');
require_once('ComparisonOperation.php');


/**
 * List of template objects (e.g. list issue, list section etc.)
 *
 */
abstract class ListObject
{
	/**
	 * The name of the list
	 *
	 * @var string
	 */
	private $m_name;

	/**
	 * The start element number from which to generate the list.
	 *
	 * @var int
	 */
	private $m_start;

	/**
	 * The maximum number of objects in the list, 0 for no limit
	 *
	 * @var int
	 */
	private $m_limit;

	/**
	 * True if the generated list has more elements than $this->m_limit
	 *
	 * @var bool
	 */
	private $m_hasNextElements;

	/**
	 * The number of columns (for generating tables)
	 *
	 * @var int
	 */
	private $m_columns;

	/**
	 * The initial constraints string.
	 *
	 * @var string
	 */
	private $m_constraintsStr;

	/**
	 * The initial order constraints string.
	 *
	 * @var string
	 */
	private $m_orderStr;

	/**
	 * The constraints array
	 *
	 * @var array
	 */
	private $m_constraints;

	/**
	 * The order constraints array
	 *
	 * @var array
	 */
	private $m_order;

	/**
	 * The list of objects.
	 *
	 * @var array
	 */
	protected $m_objects;

	/**
	 * constructor
	 *
	 * @param int $p_start
	 * @param int $p_limit
	 * @param string $p_constraints
	 * @param string $p_orderStr
	 * @param int $p_columns
	 * @param string $p_name
	 */
	public function __construct($p_start = 0, $p_limit = 0, $p_constraintsStr = null,
								$p_orderStr = null, $p_columns = 0, $p_name = null)
	{
		$this->m_start = is_numeric($p_start) ? $p_start : 0;
		$this->m_limit = is_numeric($p_limit) ? $p_limit : 0;
		$this->m_constraints = $p_constraintsStr;
		$this->m_constraints = $this->processConstraints($p_constraintsStr);
		$this->m_orderStr = $p_orderStr;
		$this->m_order = $this->processOrderString($p_orderStr);
		$this->m_columns = $p_columns;
		$this->m_name = is_string($p_name) && trim($p_name) != '' ? $p_name : $this->defaultName();
		$objects = $this->createList($p_start, $p_limit, $this->m_hasNextElements);
		$this->m_objects = new ArrayObject($objects);
	}

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
	abstract public function createList($p_start = 0, $p_limit = 0, &$p_hasNextElements);

	/**
	 * Processes list constraints passed in a string.
	 *
	 * @param string $p_constraintsStr
	 * @return array
	 */
	abstract public function processConstraints($p_constraintsStr);

	/**
	 * Processes order constraints passed in a string.
	 *
	 * @param string $p_orderStr
	 * @return array
	 */
	abstract public function processOrderString($p_orderStr);

	/**
	 * Generates a unique name for this list object.
	 *
	 * @return string
	 */
	public function defaultName()
	{
		return sha1(time());
	}

	/**
	 * Returns an iterator for this list.
	 *
	 * @return object of type ArrayIterator
	 */
	public function iterator()
	{
		return $this->m_objects->getIterator();
	}

	/**
	 * Returns true if the list is limited to a certain number of elements.
	 *
	 * @return bool
	 */
	public function isLimited()
	{
		return $this->m_limit != 0;
	}

	/**
	 * Returns the maximum number of elements, 0 for limitless.
	 *
	 * @return int
	 */
	public function getLimit()
	{
		return $this->m_limit;
	}

	/**
	 * Returns the number of the start element of this list in the
	 * original list from which this was truncated.
	 *
	 * @return unknown
	 */
	public function getStart()
	{
		return $this->m_start;
	}

	/**
	 * Returns the number of the last element of this list in the
	 * original list from which this was truncated.
	 *
	 * @return unknown
	 */
	public function getEnd()
	{
		return $this->m_start + $this->m_objects->count();
	}

	/**
	 * Returns true if this list is limited and elements still exist
	 * in the original list (from which this was truncated) after the
	 * last element of this list.
	 *
	 * @return bool
	 */
	public function hasNextElements()
	{
		return $this->m_hasNextElements;
	}

	/**
	 * Returns the column number for the given iterator
	 *
	 * @param object of type ArrayIterator $p_iterator
	 */
	public function getColumn($p_iterator)
	{
		return $p_iterator->key() % $this->m_columns;
	}

	/**
	 * Returns the number of columns.
	 *
	 * @return int
	 */
	public function columns()
	{
		return $this->m_columns;
	}
}

?>