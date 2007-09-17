<?php

require_once('Operator.php');
require_once('ComparisonOperation.php');


/**
 * DO NOT DELETE THIS CLASS. Deleting this class will generate hazardous
 * behavior of the iterator: after reaching the array end, the next()
 * method will rewind the pointer.
 *
 */
class MyArrayObject extends ArrayObject
{
	public function getIterator()
	{
		return new ArrayIterator($this);
	}
}


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
	protected $m_constraints;

	/**
	 * The order constraints array
	 *
	 * @var array
	 */
	protected $m_order;

	/**
	 * The list of objects.
	 *
	 * @var array
	 */
	protected $m_objects;

	/**
	 * The default iterator for the current object.
	 *
	 * @var object
	 */
	protected $m_defaultIterator = null;

	/**
	 * constructor
	 * For blank lists the start element index ($p_start) is smaller
	 * than 0.
	 *
	 * @param int $p_start
	 * @param string $p_parameters
	 */
	public function __construct($p_start = 0, $p_parameters = array())
	{
		/**
		 * For blank lists the start element index ($p_start) is smaller
		 * than 0.
		 */
		if ($p_start < 0) {
			$this->m_start = -1;
			$this->m_limit = -1;
			$this->m_columns = 0;
			$this->m_objects = new MyArrayObject(array());
			return;
		}

		/**
		 * Processes the input parameters passed in an array; drops the invalid
		 * parameters and parameters with invalid values. Returns an array of
		 * valid parameters.
		 */
		$parameters = $this->ProcessParameters($p_parameters);

		/**
		 * Set common parameters:
		 * - start element index (m_start)
		 * - maximum list length (m_limit)
		 * - list columns (m_columns)
		 * - constraints string (m_constraintsStr)
		 * - order string (m_orderStr)
		 * - list name (m_name)
		 */
		$this->m_start = is_numeric($p_start) ? $p_start : 0;
		$this->m_limit = isset($parameters['length']) ? $parameters['length'] : 0;
		$this->m_columns = isset($parameters['columns']) ? $parameters['columns'] : 0;
		$this->m_constraintsStr = isset($parameters['constraints']) ? $parameters['constraints'] : '';
		$this->m_orderStr = isset($parameters['order']) ? $parameters['order'] : '';
		$name = isset($parameters['name']) ? $parameters['name'] : '';
		$this->m_name = is_string($name) && trim($name) != '' ? $name : $this->defaultName();

		/**
		 * Process the list constraints.
		 */
		$this->m_constraints = $this->ProcessConstraints(ListObject::ParseConstraintsString($this->m_constraintsStr));

		/**
		 * Process order constraints.
		 */
		$this->m_order = $this->ProcessOrder(ListObject::ParseConstraintsString($this->m_orderStr));

		$objects = $this->CreateList($this->m_start, $this->m_limit, $this->m_hasNextElements, $parameters);
		if (!is_array($objects)) {
		    $objects = array();
		}
  		$this->m_objects = new MyArrayObject($objects);
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
	 * @param array $p_parameters
	 * @return array
	 */
	abstract protected function CreateList($p_start = 0, $p_limit = 0, &$p_hasNextElements, $p_parameters);

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	abstract protected function ProcessConstraints($p_constraints);

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param string $p_order
	 * @return array
	 */
	abstract protected function ProcessOrder($p_order);

	/**
	 * Processes the input parameters passed in an array; drops the invalid
	 * parameters and parameters with invalid values. Returns an array of
	 * valid parameters.
	 *
	 * @param array $p_parameters
	 * @return array
	 */
	abstract protected function ProcessParameters($p_parameters);

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
	 * Returns the default iterator of this list.
	 *
	 * @return object of type ArrayIterator
	 */
	public function defaultIterator()
	{
		if (!isset($this->m_defaultIterator)) {
			$this->m_defaultIterator = $this->getIterator();
		}
		return $this->m_defaultIterator;
	}

	/**
	 * Returns the current element of the default iterator.
	 *
	 * @return object
	 */
	public function getCurrent()
	{
		if ($this->isEmpty()) {
			return null;
		}
		return $this->defaultIterator()->current();
	}

	/**
	 * Returns the index of the current element of the default iterator.
	 *
	 * @return int
	 */
	public function getIndex()
	{
		if ($this->isEmpty()) {
			return 0;
		}
		return 1 + $this->defaultIterator()->key();
	}

	/**
	 * Returns an iterator for this list.
	 *
	 * @return object of type ArrayIterator
	 */
	public function getIterator()
	{
		return $this->m_objects->getIterator();
	}

	/**
	 * Returns the list name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->m_name;
	}

	/**
	 * Returns the length of the list.
	 *
	 * @return int
	 */
	public function getLength()
	{
		return $this->m_objects->count();
	}

	/**
	 * Return true if the list is blank (see the constructor documentation).
	 *
	 * @return bool
	 */
	public function isBlank()
	{
		return $this->m_start < 0;
	}

	/**
	 * Return true if the list is empty.
	 *
	 * @return bool
	 */
	public function isEmpty()
	{
		return $this->m_objects->count() == 0;
	}

	/**
	 * Returns true if the list is limited to a certain number of elements.
	 *
	 * @return bool
	 */
	public function isLimited()
	{
		return $this->m_limit > 0;
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
	 * Returns the index of the start element of this list in the
	 * original list from which this was truncated.
	 *
	 * @return int
	 */
	public function getStart()
	{
		return $this->m_start;
	}

	/**
	 * Returns the index of the last element of this list in the
	 * original list from which this was truncated.
	 *
	 * @return int
	 */
	public function getEnd()
	{
		return $this->m_start + $this->m_objects->count();
	}

	/**
	 * Returns true if the index of the start element in the
	 * original list - from which this was truncated - is greater
	 * than 0.
	 *
	 * @return bool
	 */
	public function hasPreviousElements()
	{
		return $this->m_start > 0;
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
	 * @param int $p_iterator
	 */
	public function getColumn($p_iterator = null)
	{
		if (!isset($p_iterator)) {
			$p_iterator = $this->defaultIterator();
		}
		if ($this->m_columns == 0 || $p_iterator->count() == 0) {
			return 0;
		}
		return 1 + ($p_iterator->key() % $this->m_columns);
	}

	/**
	 * Returns the number of columns.
	 *
	 * @return int
	 */
	public function getColumns()
	{
		return $this->m_columns;
	}

	/**
	 * Returns the constraints string.
	 *
	 * @return string
	 */
	public function getConstraintsString()
	{
		return $this->m_constraintsStr;
	}

	/**
	 * Returns the order string.
	 *
	 * @return string
	 */
	public function getOrderString()
	{
		return $this->m_orderStr;
	}

    /**
     * Overloaded method call to give access to the list properties.
     *
     * @param string $p_element - the property name
     * @return mix - the property value
     */
	public function __get($p_property)
	{
	    $p_property = strtolower($p_property);
	    switch ($p_property) {
	        case 'column':
	            return $this->getColumn($this->defaultIterator());
	        case 'columns':
	            return $this->getColumns();
	        case 'current':
	            return $this->getCurrent();
	        case 'end':
	            return $this->getEnd();
	        case 'index':
	            return $this->getIndex();
	        case 'length':
	            return $this->getLength();
	        case 'limit':
	            return $this->getLimit();
	        case 'name':
	            return $this->getName();
	        case 'start':
	            return $this->getStart();
	    }
	}

	/**
	 * Parses the constraints string and returns an array of words
	 *
	 * @param string $p_constraintsString
	 * @return array
	 */
	public static function ParseConstraintsString($p_constraintsString)
	{
	    if (empty($p_constraintsString)) {
	        return array();
	    }

	    $words = array();
	    $escaped = false;
	    $lastWord = '';
	    foreach (str_split($p_constraintsString) as $char) {
	        if (preg_match('/[\s]/', $char) && !$escaped) {
	            if (!empty($lastWord)) {
	                $words[] = $lastWord;
	                $lastWord = '';
	            }
	        } elseif ($char == "\\" && !$escaped) {
	            $escaped = true;
	        } else {
	            $lastWord .= $char;
    	        $escaped = false;
	        }
	    }
	    if (strlen($lastWord) > 0) {
	        $words[] = $lastWord;
	    }
	    return $words;
	}
}

?>