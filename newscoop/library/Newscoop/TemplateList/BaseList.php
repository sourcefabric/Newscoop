<?php

/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\TemplateList;

use Newscoop\Criteria;
use Newscoop\ListResult;

/**
 * Base class for list of template objects (e.g. list issue, list section etc.)
 */
abstract class BaseList 
{
    /**
     * The identifier of the list
     *
     * @var string
     */
    private $id;

    /**
     * The name of the list
     *
     * @var string
     */
    private $name;

    /**
     * The start page number from which to generate the list.
     *
     * @var int
     */
    protected $firstResult;

    /**
     * The maximum number of objects in the list, null or 0 for no limit
     *
     * @var int
     */
    protected $maxResults;

    /**
     * True if the generated list has more elements than $this->m_limit
     *
     * @var bool
     */
    private $hasNextResults;

    /**
     * The number of columns (for generating tables)
     *
     * @var int
     */
    protected $columns;

    /**
     * The initial constraints string.
     *
     * @var string
     */
    private $constraintsStr;

    /**
     * The clean array of parameters
     * @var array
     */
    protected $parameters;

    /**
     * The Criteria object
     *
     * @var Criteria
     */
    protected $critera;

    /**
     * The list of objects.
     *
     * @var Newscoop\ListResult
     */
    protected $objectsList;

    /**
     * The total number of elements in the list without the limits.
     *
     * @var integer
     */
    protected $totalCount;

    /**
     * The default iterator for the current object.
     *
     * @var object
     */
    protected $defaultIterator = null;

    /**
     * List cache key
     * @var mixed
     */
    protected $cacheKey = null;

    protected $operatorsMap = array(
        'is' => '=',
        'smaller_equal' => '<=',
        'greater_equal' => '>=',
        'smaller' => '<',
        'greater' => '>',
        'not' => '<>',
        'like' => 'like',
        'match'=> 'match'
    );

    /**
     * Default cache time
     * @var integer
     */
    protected $defaultTTL = 600;

    public function __construct($criteria)
    {
        $this->setCriteria($criteria);
    }

    /**
     * Set Criteria object
     * @param Criteria $criteria
     */
    protected function setCriteria($criteria)
    {
        // check if implement Newscoop/Criteria
        $this->criteria = $criteria;
    }

    public function getList($firstResult, $parameters)
    {
        $this->convertParameters($firstResult, $parameters);
        $this->convertConstraints($this->parseConstraintsString($parameters['constraints']));

        return $this->prepareList($this->criteria);
    }

    /**
     * Get ListResult object with list elements
     * 
     * @param  integer  $firstResult
     * @param  integer  $maxResults
     * @param  Criteria $criteria
     * 
     * @return ListResult
     */
    abstract protected function prepareList($criteria);

    /**
     * Convert constraints array to Criteria
     * 
     * @param  array  $constraints
     * @param  Criteria $criteria
     * 
     * @return Criteria
     */
    protected function convertConstraints($constraints){
        $perametersOperators = array();
        $constraints = array_chunk($constraints, 3, true);
        foreach ($constraints as $constraint) {
            if (count($constraint) == 3) {
                foreach ($this->criteria as $key => $value) {
                    if ($key == $constraint[0]) {
                        $perametersOperators[] = array($constraint[0], $this->operatorsMap[$constraint[1]]);
                        $this->criteria->$key = $constraint[2];
                    }
                }
            }
        }

        $this->criteria->perametersOperators = $perametersOperators;
    }

    /**
     * Convert parameters array to Criteria
     * 
     * @param  array  $parameters
     * @param  Criteria $criteria
     * 
     * @return Criteria
     */
    protected function convertParameters($firstResult, $parameters)
    {
        $this->firstResult = is_numeric($firstResult) ? (int)$firstResult : 0;
        $this->maxResults = isset($parameters['length']) ? (int)$parameters['length'] : 0;
        $this->columns = isset($parameters['columns']) ? (int)$parameters['columns'] : 0;
        $name = isset($parameters['name']) ? $parameters['name'] : '';
        $this->name = is_string($name) && trim($name) != '' ? $name : $this->defaultName();

        $this->orderString = isset($parameters['order']) ? str_replace('by', '', $parameters['order']) : '';
        $orderArray = $this->parseConstraintsString($this->orderString);

        foreach (array_chunk($orderArray, 2, true) as $order) {
            if (count($order) == 2) {
                foreach ($this->criteria as $key) {
                    if ($key == $order[0]) {
                        $this->criteria->orderBy = array($order[0], $order[1]);
                    }
                }
            }
        }


        // Set first and max results values to critera. 
        $this->criteria->firstResult = $this->firstResult;
        $this->criteria->maxResults = $this->firstResult;
    }

    /**
     * Generates a unique name for this list object.
     *
     * @return string
     */
    private function defaultName()
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
        
    }

    /**
     * Returns the current element of the default iterator.
     *
     * @return object
     */
    public function getCurrent()
    {
        
    }

    /**
     * Returns the index of the current element of the default iterator.
     *
     * @return int
     */
    public function getIndex()
    {
        
    }

    /**
     * Returns an iterator for this list.
     *
     * @return object of type ArrayIterator
     */
    public function getIterator()
    {
        
    }

    /**
     * Returns the list name.
     *
     * @return string
     */
    public function getName()
    {
        
    }

    /**
     * Returns the length of the list.
     *
     * @return int
     */
    public function getLength()
    {
        
    }

    /**
     * Return true if the list is blank (see the constructor documentation).
     *
     * @return bool
     */
    public function isBlank()
    {
        
    }

    /**
     * Return true if the list is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        
    }

    /**
     * Returns true if the list is limited to a certain number of elements.
     *
     * @return bool
     */
    public function isLimited()
    {
        
    }

    /**
     * Returns the maximum number of elements, 0 for limitless.
     *
     * @return int
     */
    public function getLimit()
    {
        
    }

    /**
     * Returns the index of the start element of this list in the
     * original list from which this was truncated.
     *
     * @return int
     */
    public function getStart()
    {

    }

    /**
     * Returns the start element index of the previous group of items
     * from the list. Returns null if the list had no limit.
     *
     * @return int
     */
    private function getPrevStart()
    {
       
    }

    /**
     * Returns the start element index of the next group of items
     * from the list. Returns null if the list had no limit.
     *
     * @return int
     */
    private function getNextStart()
    {
        
    }

    /**
     * Returns the index of the last element of this list in the
     * original list from which this was truncated.
     *
     * @return int
     */
    public function getEnd()
    {

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

    }

    /**
     * Returns the total number of elements in the list without the limits.
     *
     * @return unknown
     */
    public function getTotalCount()
    {
    }

    /**
     * Returns the column number for the given iterator
     *
     * @param int $p_iterator
     */
    public function getColumn($p_iterator = null)
    {
    
    }

    /**
     * Returns the row number for the given iterator
     *
     * @param int $p_iterator
     */
    public function getRow($p_iterator = null)
    {

    }

    /**
     * Returns the number of columns.
     *
     * @return int
     */
    public function getColumns()
    {
        
    }

    /**
     * Overloaded method call to give access to the list properties.
     *
     * @param string $property - the property name
     * 
     * @return mixed    the property value
     */
    public function __get($property)
    {
       $property = strtolower($property);
        switch ($property) {
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
                return $this->id;
            default:
                $errorMessage = INVALID_PROPERTY_STRING . " $property "
                                . OF_OBJECT_STRING . ' list';
                CampTemplate::singleton()->trigger_error($errorMessage);
        }
    }

    /**
     * Parses the constraints string and returns an array of words
     *
     * @param string $constraintsString
     * @return array
     */
    private function parseConstraintsString($constraintsString)
    {
        if (empty($constraintsString)) {
            return array();
        }

        $words = array();
        $escaped = false;
        $lastWord = '';
        foreach (str_split($constraintsString) as $char) {
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

    /**
     * Sets the list identifier
     *
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }
}