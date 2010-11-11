<?php

require_once('ListObject.php');


/**
 * ImagesList class
 *
 */
class ImagesList extends ListObject
{
    private static $s_orderFields = array('default',
                                          'bydescription',
                                          'byphotographer',
                                          'bydate',
                                          'bylastupdate'
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
	    $articleImagesList = Image::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit, $p_count);
	    $metaImagesList = array();
	    foreach ($articleImagesList as $image) {
	        $metaImagesList[] = new MetaImage($image->getImageId());
	    }
	    return $metaImagesList;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
	{
		return $p_constraints;
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
	                if (array_search(strtolower($word), self::$s_orderFields) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_images, order parameter");
	                } else {
    	                $orderField = $word;
                        $state = 2;
	                }
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[] = array('field'=>$orderField, 'dir'=>$word);
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_images, order parameter");
                    }
                    $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_images");
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
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_article_images");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
    			case 'description':
    			case 'photographer':
    			case 'place':
    			case 'caption':
    			case 'date':
    			case 'type':
					$operator = new Operator('is', 'string');
					$this->m_constraints[] = new ComparisonOperation($parameter, $operator, $value);
					break;
    			case 'start_date':
					$operator = new Operator('greater_equal', 'string');
					$this->m_constraints[] = new ComparisonOperation('date', $operator, $value);
    				break;
    			case 'end_date':
					$operator = new Operator('smaller_equal', 'string');
					$this->m_constraints[] = new ComparisonOperation('date', $operator, $value);
    				break;
    			case 'description_like':
    			case 'photographer_like':
    			case 'place_like':
    			case 'caption_like':
					$operator = new Operator('match', 'string');
					$listParam = substr($parameter, 0, strlen($parameter) - strlen('_like'));
					$this->m_constraints[] = new ComparisonOperation($listParam, $operator, $value);
					break;
    			case 'search':
					$operator = new Operator('match', 'string');
					$this->m_constraints[] = new ComparisonOperation($parameter, $operator, $value);
    				break;
    			case 'local':
    				$opName = strtolower($value) == 'true' ? 'is' : 'not';
					$operator = new Operator($opName, 'string');
					$this->m_constraints[] = new ComparisonOperation($parameter, $operator, 'local');
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_images", $p_smarty);
    		}
    	}

        return $parameters;
	}
}

?>