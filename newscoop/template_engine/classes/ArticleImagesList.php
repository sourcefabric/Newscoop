<?php

require_once('ListObject.php');


/**
 * ArticleImagesList class
 *
 */
class ArticleImagesList extends ListObject
{
    private static $s_orderFields = array('default',
                                          'bynumber',
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
        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array(
            'ArticleImagesList',
            implode('-', $this->m_constraints),
            implode('-', $this->m_order),
            $p_start,
            $p_limit,
            implode('-', $p_parameters),
            $p_count
        ), 'article_image');

        if ($cacheService->contains($cacheKey)) {
            $metaImagesList = $cacheService->fetch($cacheKey);
        } else {
            $articleImagesList = ArticleImage::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit, $p_count);
            $metaImagesList = array();
            foreach ($articleImagesList as $image) {
                $metaImagesList[] = new MetaImage($image->getImageId());
            }
            $cacheService->save($cacheKey, $metaImagesList);
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
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_article_images", $p_smarty);
    		}
    	}

        $operator = new Operator('is', 'integer');
        $context = CampTemplate::singleton()->context();
        if (!$context->article->defined) {
        	CampTemplate::singleton()->trigger_error("undefined environment attribute 'Article' in statement list_article_images");
        	return false;
        }
        $this->m_constraints[] = new ComparisonOperation('nrarticle', $operator,
                                                         $context->article->number);
        $this->m_constraints[] = new ComparisonOperation('status', $operator, 'approved');

        return $parameters;
	}
}

?>
