<?php

use Newscoop\Search\Query;

require_once __DIR__ . '/ListObject.php';

/**
 * SearchResultsList class
 */
class SearchResultsList extends ListObject
{
    private static $s_orderFields = array(
        'bynumber',
        'byname',
        'bydate',
        'bycreationdate',
        'bypublishdate',
        'bylanguage',
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
        $index = Zend_Registry::get('container')->getService('search.index');
        $query = new Query(array(
            'core' => $p_parameters['language'],
            'q' => $p_parameters['q'],
            'qf' => $p_parameters['qf'],
            'rows' => $p_parameters['length'],
            'start' => $p_parameters['start'],
            'sort' => !empty($p_parameters['sort']) ? $p_parameters['sort'] : null,
        ));

        try {
            $result = $index->find($query);
        } catch (Exception $e) {
            $p_count = 0;
            return array();
        }

        if ($result) {
            $p_count = $result->numFound;
            $languageId = Language::GetLanguageIdByCode($p_parameters['language']);
            $articles = array();
	        foreach ($result->docs as $doc) {
	            $articles[] = new MetaArticle($languageId, $doc->number);
	        }

            return $articles;
        } else {
            $p_count = 0;
            return array();
        }
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
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_search_results, order parameter");
	                } else {
    	                $orderField = $word;
                        $state = 2;
	                }
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[] = array('field'=>$orderField, 'dir'=>$word);
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_search_results, order parameter");
                    }
                    $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_search_results");
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
        return $p_parameters;
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
