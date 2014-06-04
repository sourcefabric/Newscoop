<?php

require_once('ListObject.php');


/**
 * BoxArticlesList class
 *
 */
class BoxArticlesList extends ListObject
{
    private static $s_parameters = array();

    private static $s_orderFields = array(
        'bynumber',
        'byname',
        'bydate',
        'bycreationdate',
        'bypublishdate'
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
        if (!$context->article->defined()) {
            return array();
        }

        if (!$context->language->defined()) {
            $languageId = $context->publication->default_language->number;
        } else {
            $languageId = $context->language->number;
        }

        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array('BoxArticlesList', $context->article->number, implode('-', $this->m_constraints), implode('-', $this->m_order), $p_start, $p_limit, $p_count), 'boxarticles');
        if ($cacheService->contains($cacheKey)) {
            $BoxArticlesList = $cacheService->fetch($cacheKey);
        } else {
            $contextBox = new ContextBox(null, $context->article->number);
            $p_parameters['context_box'] = $contextBox->getId();
            $p_parameters['article'] = $context->article->number;

            $BoxArticlesList = ContextBoxArticle::GetList($p_parameters, $this->m_order, $p_start, $p_limit, $p_count);
            $cacheService->save($cacheKey, $BoxArticlesList);
        }
        $preview = $context->preview;
        $metaBoxArticlesList = array();
        foreach ($BoxArticlesList as $articleNo) {
            $article = new MetaArticle($languageId, $articleNo);
            if ($article->defined() && ($preview || $article->is_published)) {
                $metaBoxArticlesList[] = $article;
            }
	}

        return $metaBoxArticlesList;
    }

    /**
     * Processes list constraints passed in an array.
     *
     * @param array $p_constraints
     * @return array
     */
    protected function ProcessConstraints(array $p_constraints)
    {
        $parameters = array();
        $state = 1;
        $attribute = null;
        $operator = null;
        $value = null;
        foreach ($p_constraints as $word) {
            switch ($state) {
                case 1: // reading the parameter name
                    if (!array_key_exists($word, BoxArticlesList::$s_parameters)) {
                        CampTemplate::singleton()->trigger_error("invalid attribute $word in statement list_box_articles, constraints parameter");
                        return false;
                    }
                    $attribute = $word;
                    $state = 2;
                    break;
                case 2: // reading the operator
	                $type = BoxArticlesList::$s_parameters[$attribute]['type'];
	                try {
	                    $operator = new Operator($word, $type);
	                }
	                catch (InvalidOperatorException $e) {
	                    CampTemplate::singleton()->trigger_error("invalid operator $word of parameter constraints.$attribute in statement list_box_articles");
	                    return false;
	                }
	                $state = 3;
	                break;
	            case 3: // reading the value to compare against
	                $type = BoxArticlesList::$s_parameters[$attribute]['type'];
	                $metaClassName = 'Meta'.strtoupper($type[0]).strtolower(substr($type, 1));
	                try {
	                    $value = new $metaClassName($word);
    	                $value = $word;
       	                $comparisonOperation = new ComparisonOperation($attribute, $operator, $value);
    	                $parameters[] = $comparisonOperation;
	                } catch (InvalidValueException $e) {
	                    CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_box_articles");
	                    return false;
	                }
	                $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of constraints parameter in list_box_articles");
            return false;
	    }

		return $parameters;
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
	                if (array_search(strtolower($word), BoxArticlesList::$s_orderFields) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_box_articles, order parameter");
	                } else {
    	                $orderField = $word;
                        $state = 2;
	                }
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[] = array('field'=>$orderField, 'dir'=>$word);
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_box_articles, order parameter");
                    }
                    $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_box_articles");
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
                    case 'role':
                        if ($parameter == 'length' || $parameter == 'columns') {
                            $intValue = (int)$value;
                            if ("$intValue" != $value || $intValue < 0) {
                                CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_box_articles");
                            }
                            $parameters[$parameter] = (int)$value;
                        } else {
                            $parameters[$parameter] = $value;
                        }
                        break;
                    default:
                        CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_box_articles", $p_smarty);
                }
            }

            return $parameters;
        }
}

?>
