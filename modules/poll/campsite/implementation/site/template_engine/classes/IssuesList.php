<?php

require_once('ListObject.php');


/**
 * IssuesList class
 *
 */
class IssuesList extends ListObject
{
    private static $s_parameters = array('number'=>array('field'=>'Number', 'type'=>'int'),
                                         'name'=>array('field'=>'Name', 'type'=>'string'),
                                         'publish_date'=>array('field'=>'PublicationDate',
                                                               'type'=>'date'),
                                         'publish_year'=>array('field'=>'YEAR(PublicationDate)',
                                                               'type'=>'int'),
                                         'publish_month'=>array('field'=>'MONTH(PublicationDate)',
                                                                'type'=>'int'),
                                         'publish_mday'=>array('field'=>'MDAY(PublicationDate)',
                                                               'type'=>'int')
                                   );

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
	protected function CreateList($p_start = 0, $p_limit = 0, &$p_hasNextElements)
	{
		if ($p_start < 1) {
			$p_start = 1;
		}
		$issuesList = array('1', '2', '3', '4', '5', '6', '7', '8', '9');
		$p_hasNextElements = $p_limit > 0
							&& (($p_start + $p_limit - 1) < count($issuesList));
		if ($p_limit > 0) {
			return array_slice($issuesList, $p_start - 1, $p_limit);
		}
		return array_slice($issuesList, $p_start - 1);
	}

	/**
	 * Processes list constraints passed in a string.
	 *
	 * @param string $p_constraintsStr
	 * @return array
	 */
	protected function ProcessConstraints($p_constraintsStr)
	{
		return array();
	}

	/**
	 * Processes order constraints passed in a string.
	 *
	 * @param string $p_orderStr
	 * @return array
	 */
	protected function ProcessOrderString($p_orderStr)
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
	protected function ProcessParameters($p_parameters)
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
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_issues");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_issues", $p_smarty);
    		}
    	}
    	return $parameters;
	}
}

?>