<?php

require_once('ListObject.php');


/**
 * SearchResultsList class
 *
 */
class SearchResultsList extends ListObject
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
	 * @param array $p_parameters
	 * @return array
	 */
	protected function CreateList($p_start = 0, $p_limit = 0, &$p_hasNextElements, array $p_parameters)
	{
		if ($p_start < 1) {
			$p_start = 1;
		}
		$searchResultsList = array('1', '2', '3', '4', '5', '6', '7', '8', '9');
		$p_hasNextElements = $p_limit > 0
							&& (($p_start + $p_limit - 1) < count($searchResultsList));
		if ($p_limit > 0) {
			return array_slice($searchResultsList, $p_start - 1, $p_limit);
		}
		return array_slice($searchResultsList, $p_start - 1);
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
	{
		return array();
	}

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param array $p_order
	 * @return array
	 */
	protected function ProcessOrder(array $p_order)
	{
		return array();
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
    				if ($parameter == 'length' || $parameter == 'columns') {
    					$intValue = (int)$value;
    					if ("$intValue" != $value || $intValue < 0) {
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_search_results");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_search_results", $p_smarty);
    		}
    	}
    	return $parameters;
	}
}

?>