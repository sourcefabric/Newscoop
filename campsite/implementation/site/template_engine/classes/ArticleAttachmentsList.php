<?php

require_once('ListObject.php');


/**
 * ArticleAttachmentsList class
 *
 */
class ArticleAttachmentsList extends ListObject
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
	 * @return array
	 */
	protected function CreateList($p_start = 0, $p_limit = 0, &$p_hasNextElements, $p_parameters)
	{
	    $operator = new Operator('is', 'integer');
	    $context = CampTemplate::singleton()->context();
	    if (!$context->article->defined) {
	        return array();
	    }
	    $comparisonOperation = new ComparisonOperation('article_number', $operator,
	                                                   $context->article->number);
	    $this->m_constraints[] = $comparisonOperation;

        if (isset($p_parameters['all_languages'])
                && strtolower($p_parameters['all_languages']) != 'true'
                && $context->language->defined) {
            $comparisonOperation = new ComparisonOperation('language_id', $operator,
                                                           $context->language->number);
            $this->m_constraints[] = $comparisonOperation;
	    }

	    $articleAttachmentsList = ArticleAttachment::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit);
	    $metaAttachmentsList = array();
	    foreach ($articleAttachmentsList as $attachment) {
	        $metaAttachmentsList[] = new MetaAttachment($attachment->getAttachmentId());
	    }
	    return $metaAttachmentsList;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints($p_constraints)
	{
		return array();
	}

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param string $p_order
	 * @return array
	 */
	protected function ProcessOrder($p_order)
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
    			case 'all_languages':
    				if ($parameter == 'length' || $parameter == 'columns') {
    					$intValue = (int)$value;
    					if ("$intValue" != $value || $intValue < 0) {
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_article_attachments");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_article_attachments", $p_smarty);
    		}
    	}
    	return $parameters;
	}
}

?>