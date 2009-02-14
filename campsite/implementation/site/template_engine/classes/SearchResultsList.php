<?php

require_once('ListObject.php');


/**
 * SearchResultsList class
 *
 */
class SearchResultsList extends ListObject
{
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
	    $operator = new Operator('is', 'integer');
	    $context = CampTemplate::singleton()->context();
	    if ($p_parameters['search_level'] >= MetaActionSearch_Articles::SEARCH_LEVEL_PUBLICATION
	    && $context->publication->defined) {
	        $this->m_constraints[] = new ComparisonOperation('Articles.IdPublication', $operator,
	                                                         $context->publication->identifier);
	    }
	    if ($p_parameters['search_level'] >= MetaActionSearch_Articles::SEARCH_LEVEL_ISSUE
	    && $context->issue->defined && $p_parameters['search_issue'] == 0) {
	        $this->m_constraints[] = new ComparisonOperation('Articles.NrIssue', $operator,
	                                                         $context->issue->number);
	    }
	    if ($p_parameters['search_level'] >= MetaActionSearch_Articles::SEARCH_LEVEL_SECTION
	    && $context->section->defined && $p_parameters['search_section'] == 0) {
	        $this->m_constraints[] = new ComparisonOperation('Articles.NrSection', $operator,
	                                                         $context->section->number);
	    }
        if ($p_parameters['search_issue'] != 0) {
            $this->m_constraints[] = new ComparisonOperation('Articles.NrIssue', $operator,
                                                             $p_parameters['search_issue']);
        }
        if ($p_parameters['search_section'] != 0) {
            $this->m_constraints[] = new ComparisonOperation('Articles.NrSection', $operator,
                                                             $p_parameters['search_section']);
        }
        if (!empty($p_parameters['start_date'])) {
            $startDateOperator = new Operator('greater_equal', 'date');
        	$this->m_constraints[] = new ComparisonOperation('Articles.PublishDate', $startDateOperator,
                                                             $p_parameters['start_date']);
        }
        if (!empty($p_parameters['end_date'])) {
            $endDateOperator = new Operator('smaller_equal', 'date');
        	$this->m_constraints[] = new ComparisonOperation('Articles.PublishDate', $endDateOperator,
                                                             $p_parameters['end_date']);
        }
        if (!empty($p_parameters['topic_id'])) {
            $this->m_constraints[] = new ComparisonOperation('ArticleTopics.TopicId', $operator,
                                                             $p_parameters['topic_id']);
        }

	    $keywords = preg_split('/[\s,.-]/', $p_parameters['search_phrase']);

	    if ($p_parameters['scope'] == 'index') {
	    	$articlesList = Article::SearchByKeyword($keywords,
	    	                $p_parameters['match_all'],
	    	                $this->m_constraints,
	    	                $this->m_order,
	    	                $p_start, $p_limit, $p_count);
	    } else {
            $articlesList = Article::SearchByField($keywords,
                            $p_parameters['scope'],
                            $p_parameters['match_all'],
                            $this->m_constraints,
                            $this->m_order,
                            $p_start, $p_limit, $p_count);
	    }

	    $metaArticlesList = array();
	    foreach ($articlesList as $article) {
	        $metaArticlesList[] = new MetaArticle($article->getLanguageId(),
	                                              $article->getArticleNumber());
	    }
		return $metaArticlesList;
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
	                if (array_search(strtolower($word), SearchResultsList::$s_orderFields) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_searchresult, order parameter");
	                } else {
    	                $orderField = $word;
                        $state = 2;
	                }
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[] = array('field'=>$orderField, 'dir'=>$word);
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_searchresult, order parameter");
                    }
                    $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_searchresult");
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
    			case 'template':
    			case 'match_all':
    			case 'search_level':
    			case 'search_phrase':
    			case 'search_results':
                case 'search_issue':
                case 'search_section':
    			case 'start_date':
                case 'end_date':
                case 'topic_id':
                case 'scope':
    				if ($parameter == 'length' || $parameter == 'columns'
    				|| $parameter == 'search_level' || $parameter == 'search_section') {
    					$intValue = (int)$value;
    					if ("$intValue" != $value || $intValue < 0) {
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_search_results");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_search_results", $p_smarty);
    		}
    	}
    	return $parameters;
	}
}

?>