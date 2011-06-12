<?php

require_once('ListObject.php');


/**
 * ArticleCommentsList class
 *
 */
class ArticleCommentsList extends ListObject
{
    private static $s_orderFields = array('bydate', 'default');

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
	    global $controller;
        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
        $cols = array('time_created' => 'bydate', 'thread_order' => 'default');

        $filter = array();
        $filter = $this->m_constraints;
        $filter['status'] = 'approved';
        $params = array(
            'sFilter' => $filter
        );
        if($p_limit) {
            $params['iDisplayStart'] = $p_start;
            $params['iDisplayLength'] = $p_limit;
        }
        foreach($this->m_order as $order)
        {
            $index = $cols[$order['field']];
            if($order['field'] == 'bydate') {
                $params['iSortCol_0'] = 0;
                $params['sSortDir_0'] = $order['dir'];
            }
            elseif($order['field'] == 'default')
            {
                $params['iSortCol_1'] = true;
                $params['sSortDir_1'] = $order['dir'];
            }
        }
	    //$p_count = $repository->getCount($params, $cols);
        $articleCommentsList = $repository->getData($params, $cols);
	    foreach ($articleCommentsList as $comment)
	    {
	        $metaCommentsList[] = new MetaComment($comment->getId());
	    }
	    return $metaCommentsList;
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
	                if (array_search(strtolower($word), ArticleCommentsList::$s_orderFields) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_article_comments, order parameter");
	                } else {
    	                $orderField = $word;
                        $state = 2;
	                }
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[] = array('field'=>$orderField, 'dir'=>$word);
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_article_comments, order parameter");
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
        $parameters['ignore_language'] = false;
        $parameters['ignore_article'] = false;
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
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_article_comments");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
                case 'ignore_language':
                case 'ignore_article':
                	$value = isset($value) && strtolower($value) != 'false';
                    $parameters[$parameter] = $value;
                    break;
                default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_article_comments", $p_smarty);
    		}
    	}

        $operator = new Operator('is', 'integer');
        $context = CampTemplate::singleton()->context();

        if (!$parameters['ignore_article']) {
            if (!$context->article->defined) {
                CampTemplate::singleton()->trigger_error("undefined environment attribute 'Article' in statement list_article_comments");
                return false;
            }
            $this->m_constraints['thread'] = $context->article->number;
        } else {
            $order = array();
            foreach ($this->m_order as $orderCond) {
                if ($orderCond['field'] == 'bydate') {
                    $order[] = $orderCond;
                }
            }
            if (count($order) == 0) {
                $this->m_order[] = array('field'=>'bydate', 'dir'=>'desc');
            } else {
                $this->m_order = $order;
            }
        }

        if (!$parameters['ignore_language']) {
            if (!$context->language->defined) {
                CampTemplate::singleton()->trigger_error("undefined environment attribute 'Language' in statement list_article_comments");
                return false;
            }
            $this->m_constraints['language'] = $context->language->number;
        }

    	return $parameters;
	}
}

?>