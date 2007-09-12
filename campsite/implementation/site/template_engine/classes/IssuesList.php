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
                                         'publish_mday'=>array('field'=>'DAYOFMONTH(PublicationDate)',
                                                               'type'=>'int'),
                                         'year'=>array('field'=>'YEAR(PublicationDate)',
                                                   'type'=>'int'),
                                         'mon_nr'=>array('field'=>'MONTH(PublicationDate)',
                                                   'type'=>'int'),
                                         'mday'=>array('field'=>'DAYOFMONTH(PublicationDate)',
                                                   'type'=>'int'),
                                         'yday'=>array('field'=>'DAYOFYEAR(PublicationDate)',
                                                   'type'=>'int'),
                                         'wday'=>array('field'=>'DAYOFWEEK(PublicationDate)',
                                                   'type'=>'int'),
                                         'hour'=>array('field'=>'HOUR(PublicationDate)',
                                                   'type'=>'int'),
                                         'min'=>array('field'=>'MINUTE(PublicationDate)',
                                                   'type'=>'int'),
                                         'sec'=>array('field'=>'SECOND(PublicationDate)',
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
	    $operator = new Operator('is');
	    $context = CampTemplate::singleton()->context();
	    $comparisonOperation = new ComparisonOperation('IdPublication', $operator,
	                                                   $context->publication->identifier);
	    $this->m_constraints[] = $comparisonOperation;
	    $comparisonOperation = new ComparisonOperation('IdLanguage', $operator,
	                                                   $context->language->number);
	    $this->m_constraints[] = $comparisonOperation;

	    $issuesList = Issue::GetList($this->m_constraints);
	    $metaIssuesList = array();
	    foreach ($issuesList as $issue) {
	        $metaIssuesList[] = new MetaIssue($issue->getPublicationId(),
	                                          $issue->getLanguageId(),
	                                          $issue->getIssueNumber());
	    }
	    return $metaIssuesList;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints($p_constraints)
	{
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
	            case 1:
	                if (!array_key_exists($word, IssuesList::$s_parameters)) {
	                    CampTemplate::singleton()->trigger_error("invalid attribute $word in list_issues, constraints parameter");
	                    break;
	                }
	                $attribute = $word;
	                $state = 2;
	                break;
	            case 2:
	                $type = IssuesList::$s_parameters[$attribute];
	                try {
	                    $operator = new Operator($word, $type);
	                }
	                catch (InvalidOperatorException $e) {
	                    CampTemplate::singleton()->trigger_error("invalid operator $word for attribute $attribute in list_issues, constraints parameter");
	                    $state = 1;
	                    break;
	                }
	                $state = 3;
	                break;
	            case 3:
	                $value = $word;
	                $comparisonOperation = new ComparisonOperation($attribute, $operator, $value);
	                $parameters[] = $comparisonOperation;
	                $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of constraints parameter in list_issues");
	    }

		return $parameters;
	}

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param string $p_order
	 * @return array
	 */
	protected function ProcessOrder($p_order)
	{
	    if (!is_array($p_constraints)) {
	        return null;
	    }
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