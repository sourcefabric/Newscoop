<?php
/**
 * @package Campsite
 */
class DebateAnswerAttachmentsList extends ListObject
{
    private $m_item;

	private static $s_orderFields = array(
		                              'bynumber',
		                              'byanswer',
		                              'byvotes',
		                              'bypercentage',
		                              'bypercentage_overall',
		                              'byvalue',
		                              'byaverage_value',
		                              'bylastmodified'
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
	protected function CreateList($p_start = 0, $p_limit = 0, array $p_parameters, &$p_count)
	{
	    $operator = new Operator('is');
	    $context = CampTemplate::singleton()->context();
	    $comparisonOperation = new ComparisonOperation('debate_nr', $operator,
	                                                   $context->debate->number);
	    $this->m_constraints[] = $comparisonOperation;

        $comparisonOperation = new ComparisonOperation('debateanswer_nr', $operator,
                                                        $context->debateanswer->number);
	    $this->m_constraints[] = $comparisonOperation;

	    $debateAnswerAttachmentsList = DebateAnswerAttachment::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit, $p_count);
        $metaDebateAnswerAttachmentsList = array();

	    foreach ($debateAnswerAttachmentsList as $debateAnswerAttachment) {
	        $metaDebateAnswerAttachmentsList[] = new MetaAttachment($debateAnswerAttachment->getAttachmentId());
	    }
	    return $metaDebateAnswerAttachmentsList;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
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
	        if ($state == 1) {
	                if (array_key_exists($word, DebateAnswersList::$s_parameters) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid attribute $word in list_debateanswer_attachments, constraints parameter");
	                }
	                $attribute = $word;
	                $state = 2;
	        }
	        if ($state == 2) {
	                $type = DebateAnswerAttachmentsList::$s_parameters[$attribute];
	                try {
	                    $operator = new Operator($word, $type);
	                }
	                catch (InvalidOperatorException $e) {
	                    CampTemplate::singleton()->trigger_error("invalid operator $word for attribute $attribute in list_debateanswer_attachments, constraints parameter");
	                    $state = 1;
	                    break;
	                }
	                $state = 3;
	        }

	        if ($state == 3) {
	                $value = $word;
	                $comparisonOperation = new ComparisonOperation($attribute, $operator, $value);
	                $parameters[] = $comparisonOperation;
	                $state = 1;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of constraints parameter in list_debateanswer_attachments");
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
	    if (!is_array($p_order)) {
	        return null;
	    }

	    $order = array();
	    $state = 1;
	    foreach ($p_order as $word) {
	        switch ($state) {
                case 1: // reading the order field
	                if (array_search(strtolower($word), DebateAnswerAttachmentsList::$s_orderFields) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_debateanswer_attachments, order parameter");
	                } else {
    	                $orderField = $word;
	                }
	                $state = 2;
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[$orderField] = $word;
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_debateanswer_attachments, order parameter");
                    }
                    $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_debateanswer_attachments");
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
    	$this->m_item = is_string($p_parameters['item']) && trim($p_parameters['item']) != '' ? $p_parameters['item'] : null;

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