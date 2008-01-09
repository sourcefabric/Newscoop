<?php

require_once('ListObject.php');


/**
 * SubtitlesList class
 *
 */
class SubtitlesList extends ListObject
{
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
	    $context = CampTemplate::singleton()->context();
	    if (!$context->article->defined) {
	        return array();
	    }
	    if ($context->article->type_name != $context->body_field_article_type) {
	        return array();
	    }
	    $articleData = new ArticleData($context->body_field_article_type,
	                                   $context->article->number,
	                                   $context->language->number);
	    $fieldValue = $articleData->getProperty('F'.$context->body_field_name);
	    $subtitles = MetaSubtitle::ReadSubtitles($fieldValue, $context->article->name);
	    $p_count = count($subtitles);
	    return array_slice($subtitles, $p_start, $p_limit);
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
    			case 'field_name':
    				if ($parameter == 'length' || $parameter == 'columns') {
    					$intValue = (int)$value;
    					if ("$intValue" != $value || $intValue < 0) {
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_subtitles");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_subtitles", $p_smarty);
    		}
    	}
    	return $parameters;
	}
}

?>