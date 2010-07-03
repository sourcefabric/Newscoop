<?php

require_once('ListObject.php');


/**
 * SubtopicsList class
 *
 */
class SubtopicsList extends ListObject
{
    private static $s_orderFields = array('default',
                                          'bynumber',
                                          'byname'
                                    );

	/**
	 * Creates the list of objects. Sets the parameter $p_hasNextElements to
	 * true if this list is limited and elements still exist in the original
	 * list (from which this was truncated) after the last element of this
	 * list.
	 *
	 * @param int $p_start
	 * @param int $p_limit
	 * @param array $p_parameters
	 * @param int &$p_count
	 * @return array
	 */
	protected function CreateList($p_start = 0, $p_limit = 0, array $p_parameters, &$p_count)
	{
	    $rootTopicId = $p_parameters['topic_identifier'];

	    if ($p_start > 0 || $p_limit > 0) {
	        $sqlOptions = array('LIMIT'=>array('START'=>$p_start,
											   'MAX_ROWS'=>($p_limit == 0 ? 0 : $p_limit + 1)
                                         )
                          );
	    } else {
	        $sqlOptions = null;
	    }

	    if (count($this->m_order) == 0) {
	    	$this->m_order[] = array('field'=>'default', 'dir'=>'asc');
	    }

	    $topicsList = Topic::GetTopics(null, $p_parameters['language_id'], null,
	                                   $rootTopicId, $sqlOptions, $this->m_order);
	    $p_count = Topic::GetTopics(null, $p_parameters['language_id'], null,
	                                $rootTopicId, null, null, true);
	    $metaTopicsList = array();
	    $index = 0;
	    foreach ($topicsList as $topic) {
	        $index++;
	        if ($p_limit == 0 || ($p_limit > 0 && $index <= $p_limit)) {
    	        $metaTopicsList[] = new MetaTopic($topic->getTopicId());
	        }
	    }
	    return $metaTopicsList;
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
        $order = array();
        $state = 1;
        foreach ($p_order as $word) {
            switch ($state) {
                case 1: // reading the order field
                    if (array_search(strtolower($word), SubtopicsList::$s_orderFields) === false) {
                        CampTemplate::singleton()->trigger_error("invalid order field $word in list_subtopics, order parameter");
                    } else {
                        $orderField = $word;
                        $state = 2;
                    }
                    break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[] = array('field'=>$orderField, 'dir'=>$word);
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_subtopics, order parameter");
                    }
                    $state = 1;
                    break;
            }
        }
        if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_issues");
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
    			case 'order':
    				if ($parameter == 'length' || $parameter == 'columns') {
    					$intValue = (int)$value;
    					if ("$intValue" != $value || $intValue < 0) {
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_subtopics");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_subtopics", $p_smarty);
    		}
    	}
    	// 'topic_identifier' and 'language_id' parameters are needed for the cache key
    	$context = CampTemplate::singleton()->context();
    	$parameters['topic_identifier'] = $context->topic->identifier;
    	if (is_null($parameters['topic_identifier'])) {
    		$parameters['topic_identifier'] = 0;
    	}
        $parameters['language_id'] = $context->language->number;
    	return $parameters;
	}


    protected function getCacheKey()
    {
        if (is_null($this->m_cacheKey)) {
            $this->m_cacheKey = __CLASS__ . '__' . serialize($this->m_parameters)
            . '__' . $this->m_start . '__' . $this->m_limit . '__' . $this->m_columns;
        }
        return $this->m_cacheKey;
    }
}

?>