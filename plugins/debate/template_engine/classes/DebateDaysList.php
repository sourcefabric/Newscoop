<?php
/**
 * @package Campsite
 */
class DebateDaysList extends ListObject
{
    private $m_item;

    public static $s_parameters = array();

    private static $s_orderFields = array( 'bydays', 'byweeks', 'bymonths' );

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
	protected function CreateList($p_start = 0, $p_limit = 0, array $p_parameters, &$p_count)
	{
	    $context = CampTemplate::singleton()->context();
	    $debate = new Debate($context->debate->language_id, $context->debate->number); // the template current debate
	    switch ($debate->getProperty('results_time_unit'))
	    {
	        case 'daily' : $rangeUnit = 86400; break;
	        case 'weekly' : $rangeUnit = 604800; break;
	        case 'monthly' : $rangeUnit = 2629744; break;
	    }

    	$dateStart = $context->debate->date_begin;
	    $dateEnd = ($p_limit != 0) ? strtotime(strftime('%D', $dateStart).' + '.($p_limit-1).' days') : $context->debate->date_end;

	    $dateRange = array($dateStart);
	    $dateStartString = strftime('%F %T', $dateStart);
	    while (current($dateRange) < $dateEnd)
	    {
	        $dateRange[] = strtotime($dateStartString.' + 1 day');
	        $dateStartString = strftime('%F %T', next($dateRange));
	    }
	    // @todo check the end range here for daylight savings time thing also..
	    $dateVotes = DebateVote::getResults($context->debate->number, $context->debate->language_id, $dateStart, $dateEnd+86399);

        $dateResults = array();
        foreach ($dateRange as $timestamp)
        {
            $found = 0;
            foreach ($dateVotes as $vote)
            {
                if (strftime('%D', $vote['time']) == strftime('%D', $timestamp))
                {
                    $found = $vote;
                    break;
                }
            }
            if ($found) {
                $dateResults[] = $found;
            }
            else {
                $dateResults[] = array( 'time' => $timestamp, 'total_count' => 0 );
            }
        }

        $dateArray = array();
        foreach ($dateResults as $date) {
            $dateArray[] = new MetaDebateDays($date);
        }

        return $dateArray;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
	{
	    return null;

	    if (!is_array($p_constraints)) {
	        return null;
	    }

	    $parameters = array();
	    $state = 1;
	    $attribute = null;
	    $operator = null;
	    $value = null;
	    foreach ($p_constraints as $word) {
	        switch ($state) {
	            case 1: // reading the parameter name
	                if (!array_key_exists($word, DebateVotesList::$s_parameters)) {
	                    CampTemplate::singleton()->trigger_error("invalid attribute $word in list_debateanswers, constraints parameter");
	                    break;
	                }
	                $attribute = $word;
	                $state = 2;
	                break;
	            case 2: // reading the operator
	                $type = DebateVotesList::$s_parameters[$attribute]['type'];
	                try {
	                    $operator = new Operator($word, $type);
	                }
	                catch (InvalidOperatorException $e) {
	                    CampTemplate::singleton()->trigger_error("invalid operator $word for attribute $attribute in list_debateanswers, constraints parameter");
	                }
	                $state = 3;
	                break;
	            case 3: // reading the value to compare against
	                $type = DebateVotesList::$s_parameters[$attribute]['type'];
	                $metaClassName = 'Meta'.strtoupper($type[0]).strtolower(substr($type, 1));
	                try {
	                    $value = new $metaClassName($word);
    	                $value = $word;
       	                $comparisonOperation = new ComparisonOperation($attribute, $operator, $value);
    	                $parameters[] = $comparisonOperation;
	                } catch (InvalidValueException $e) {
	                    CampTemplate::singleton()->trigger_error("invalid value $word of attribute $attribute in list_debateanswers, constraints parameter");
	                }
	                $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of constraints parameter in list_debates");
	    }

		return $parameters;
	}

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param string $p_order
	 * @return array
	 */
	protected function ProcessOrder(array $p_order)
	{
	    return null;

	    if (!is_array($p_order)) {
	        return null;
	    }

	    $order = array();
	    $state = 1;
	    foreach ($p_order as $word) {
	        switch ($state) {
                case 1: // reading the order field
	                if (array_search(strtolower($word), DebateAnswersList::$s_orderFields) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_debateanswers, order parameter");
	                } else {
    	                $orderField = $word;
	                }
	                $state = 2;
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[$orderField] = $word;
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_debateanswers, order parameter");
                    }
                    $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_debateanswers");
	    }

	    return $order;
	}

	/**
	 * Processes the input parameters passed in an array; drops the invalid
	 * parameters and parameters with invalid values. Returns an array of
	 * valid parameters.
	 *
	 * @param array $p_parameters
	 * @return array
	 */
	protected function ProcessParameters(array $p_parameters)
	{
		$parameters = array();
    	foreach ($p_parameters as $parameter=>$value) {
    		$parameter = strtolower($parameter);
    		switch ($parameter) {
    			case 'length':
    			case 'columns':
    			case 'name':
    			case 'constraints':
    			case 'order':
    			case 'item':
    				if ($parameter == 'length' || $parameter == 'columns') {
    					$intValue = (int)$value;
    					if ("$intValue" != $value || $intValue < 0) {
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_debate_answers");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_debate_answers", $p_smarty);
    		}
    	}
    	$this->m_item = (isset($p_parameters['item']) && trim($p_parameters['item']) != '') ? $p_parameters['item'] : null;
    	return $parameters;
	}


	/**
     * Overloaded method call to give access to the list properties.
     *
     * @param string $p_element - the property name
     * @return mix - the property value
     */
	public function __get($p_property)
	{
	    if (strtolower($p_property) == 'item') {
            return $this->getItem();
	    }
	    return parent::__get($p_property);
	}

	/**
	 * Returns the assignment identifier.
	 *
	 * @return int
	 */
	public function getItem()
	{
		return $this->m_item;
	}
}

?>