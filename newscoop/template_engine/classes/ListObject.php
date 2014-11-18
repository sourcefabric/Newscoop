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
     * The identifier of the list
     *
     * @var string
     */
    private $m_id;

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
	protected $m_start;

	/**
	 * The maximum number of objects in the list, 0 for no limit
	 *
	 * @var int
	 */
	protected $m_limit;

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
	protected $m_columns;

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

	protected $m_parameters;

	/**
	 * The constraints array
	 *
	 * @var array
	 */
	protected $m_constraints = array();

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
	 * The total number of elements in the list without the limits.
	 *
	 * @var integer
	 */
	protected $m_totalCount;

	/**
	 * The default iterator for the current object.
	 *
	 * @var object
	 */
	protected $m_defaultIterator = null;

	protected $m_cacheKey = null;

	protected $m_defaultTTL = 600;


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

		$this->m_id = null;

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
		$this->m_start = is_numeric($p_start) ? (int)$p_start : 0;
		$this->m_limit = isset($parameters['length']) ? (int)$parameters['length'] : 0;
		$this->m_columns = isset($parameters['columns']) ? (int)$parameters['columns'] : 0;
		$this->m_constraintsStr = isset($parameters['constraints']) ? $parameters['constraints'] : '';
		$this->m_orderStr = isset($parameters['order']) ? $parameters['order'] : '';
		$name = isset($parameters['name']) ? $parameters['name'] : '';
		$this->m_name = is_string($name) && trim($name) != '' ? $name : $this->defaultName();

		/**
		 * Process the list constraints.
		 */
		$constraints = $this->ProcessConstraints(self::ParseConstraintsString($this->m_constraintsStr));
        if ($constraints === false || $parameters === false) {
            $this->m_totalCount = 0;
            $this->m_objects = new MyArrayObject(array());
            $this->m_hasNextElements = false;
            return;
        }
        $this->m_parameters = $parameters;
        if (isset($constraints)) {
            $this->m_constraints = array_merge($this->m_constraints, $constraints);
        }

		/**
		 * Process order constraints.
		 */
		$this->m_order = $this->ProcessOrder(ListObject::ParseConstraintsString($this->m_orderStr));

        $list = $this->fetchFromCache();
        if (!is_null($list)) {
        	$this->duplicateObject($list);
        	return;
        }

		$objects = $this->CreateList($this->m_start, $this->m_limit, $parameters, $this->m_totalCount);
		if (!is_array($objects)) {
		    $objects = array();
		}
  		$this->m_objects = new MyArrayObject($objects);
  		$this->m_hasNextElements = $this->m_totalCount > ($this->m_start + $this->getLength());
  		$this->storeInCache();
	}


	/**
	 * Creates the list of objects. Sets the parameter $p_hasNextElements to
	 * true if this list is limited and elements still exist in the original
	 * list (from which this was truncated) after the last element of this
	 * list.
	 *
	 * @param int $p_start
	 * @param int $p_limit
	 * @param array &$p_parameters
	 * @param int $p_count
	 * @return array
	 */
	abstract protected function CreateList($p_start = 0, $p_limit = 0, array $p_parameters, &$p_count);

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	abstract protected function ProcessConstraints(array $p_constraints);

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param array $p_order
	 * @return array
	 */
	abstract protected function ProcessOrder(array $p_order);

	/**
	 * Processes the input parameters passed in an array; drops the invalid
	 * parameters and parameters with invalid values. Returns an array of
	 * valid parameters.
	 *
	 * @param array $p_parameters
	 * @return array
	 */
	abstract protected function ProcessParameters(array $p_parameters);


	private function fetchFromCache()
	{
        if (CampCache::IsEnabled()) {
            $object = CampCache::singleton()->fetch($this->getCacheKey());
            if ($object !== false && is_object($object)) {
                return $object;
            }
        }
        return null;
	}


	private function storeInCache()
	{
        if (CampCache::IsEnabled()) {
            CampCache::singleton()->store($this->getCacheKey(), $this, $this->m_defaultTTL);
        }
	}


	protected function getCacheKey()
	{
		if (is_null($this->m_cacheKey)) {
			$this->m_cacheKey = get_class($this) . '__' . serialize($this->m_constraints)
			. '__' . serialize($this->m_order) . '__' . $this->m_start
			. '__' . $this->m_limit . '__' . $this->m_columns;
		}
		return $this->m_cacheKey;
	}


    /**
     * Copies the given object
     *
     * @param object $p_source
     * @return object
     */
    private function duplicateObject($p_source)
    {
        foreach ($p_source as $key=>$value) {
            $this->$key = $value;
        }
    }


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
	 * @return integer|null of type ArrayIterator
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
		return 1 + $this->m_start + $this->defaultIterator()->key();
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
	 * Returns the start element index of the previous group of items
	 * from the list. Returns null if the list had no limit.
	 *
	 * @return int
	 */
	private function getPrevStart()
	{
	    if ($this->m_limit == 0) {
	        return null;
	    }
	    return ($this->m_start >= $this->m_limit ? ($this->m_start - $this->m_limit) : 0);
	}

	/**
	 * Returns the start element index of the next group of items
	 * from the list. Returns null if the list had no limit.
	 *
	 * @return int
	 */
	private function getNextStart()
	{
	    if ($this->m_limit == 0) {
	        return null;
	    }
	    return $this->m_start + $this->m_limit;
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
	 * Returns the total number of elements in the list without the limits.
	 *
	 * @return integer
	 */
	public function getTotalCount()
	{
	    return $this->m_totalCount;
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
	 * Returns the row number for the given iterator
	 *
	 * @param int $p_iterator
	 */
	public function getRow($p_iterator = null)
	{
		if (!isset($p_iterator)) {
			$p_iterator = $this->defaultIterator();
		}
		if ($this->m_columns == 0 || $this->m_columns == 1) {
			return (1 + $p_iterator->key());
		}
		return 1 + (int)($p_iterator->key() / $this->m_columns);
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
	        case 'row':
	            return $this->getRow();
	        case 'start':
	            return $this->getStart();
	        case 'count':
	            return $this->getTotalCount();
	        case 'at_beginning':
	            return $this->getIndex() == ($this->getStart() + 1);
	        case 'at_end';
	            return $this->getIndex() == $this->getEnd();
	        case 'has_next_elements':
	            return $this->hasNextElements();
	        case 'has_previous_elements':
	            return $this->hasPreviousElements();
	        case 'previous_start':
	            return $this->getPrevStart();
	        case 'next_start':
	            return $this->getNextStart();
	        case 'id':
	            return $this->m_id;
	        default:
    	        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
        				        . OF_OBJECT_STRING . ' list';
	            CampTemplate::singleton()->trigger_error($errorMessage);
	    }
	}

    /**
     * Sets the list identifier
     *
     * @param string $p_id
     */
	public function setId($p_id) {
	    $this->m_id = $p_id;
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
	            if (strlen($lastWord) > 0) {
	            	if ($lastWord == "''") {
	            		$lastWord = '';
	            	}
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
	    	if ($lastWord == "''") {
	    		$lastWord = '';
	    	}
	        $words[] = $lastWord;
	    }
	    return $words;
	}
}

?>
