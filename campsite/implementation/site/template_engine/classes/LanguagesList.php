<?php

require_once('ListObject.php');


/**
 * LanguagesList class
 *
 */
class LanguagesList extends ListObject
{
    private static $s_orderFields = array(
                                          'bynumber',
                                          'byname',
                                          'byenglish_name',
                                          'bycode'
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
	    $context = CampTemplate::singleton()->context();

        if ($p_parameters['of_article']) {
        	$metaLanguagesList = $context->article->languages_list(
        	$p_parameters['exclude_current'], $this->m_order);
        } elseif ($p_parameters['of_issue']) {
        	$metaLanguagesList = $context->issue->languages_list(
        	$p_parameters['exclude_current'], $this->m_order);
        } elseif ($p_parameters['of_publication']) {
        	$metaLanguagesList = $context->publication->languages_list(
        	$p_parameters['exclude_current'], $this->m_order);
        } else {
        	if ($p_parameters['exclude_current']) {
        		$excludeList = array($context->language->number);
        	} else {
        		$excludeList = array();
        	}
        	$languagesList = Language::GetLanguages(null, null, null,
        	$excludeList, $this->m_order);
        	$metaLanguagesList = array();
        	foreach ($languagesList as $language) {
        		$metaLanguagesList[] = new MetaLanguage($language->getLanguageId());
        	}
        }

	    return $metaLanguagesList;
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
	                if (array_search(strtolower($word), LanguagesList::$s_orderFields) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_languages, order parameter");
	                } else {
    	                $orderField = $word;
                        $state = 2;
	                }
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[] = array('field'=>$orderField, 'dir'=>$word);
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_languages, order parameter");
                    }
                    $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_languages");
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
		$parameters['of_publication'] = false;
        $parameters['of_issue'] = false;
		$parameters['of_section'] = false;
        $parameters['of_article'] = false;
        $parameters['exclude_current'] = false;
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
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_languages");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
                case 'of_publication':
                case 'of_issue':
                case 'of_article':
                case 'exclude_current':
                	$value = isset($value) && strtolower($value) != 'false';
                    $parameters[$parameter] = $value;
                    break;
    		    default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_languages", $p_smarty);
    		}
    	}
    	return $parameters;
	}


    protected function getCacheKey()
    {
        if (is_null($this->m_cacheKey)) {
            $this->m_cacheKey = get_class($this) . '__' . serialize($this->m_parameters)
            . '__' . serialize($this->m_order) . '__' . $this->m_start
            . '__' . $this->m_limit . '__' . $this->m_columns;
        }
        return $this->m_cacheKey;
    }
}

?>