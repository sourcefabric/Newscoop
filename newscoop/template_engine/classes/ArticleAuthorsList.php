<?php

require_once('ListObject.php');


/**
 * ArticleAuthorsList class
 *
 */
class ArticleAuthorsList extends ListObject
{
    private static $s_orderFields = array('default',
                                          'byfirstname',
                                          'bylastname'
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
	    $articleAuthorsList = ArticleAuthor::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit, $p_count);
	    $metaAuthorsList = array();
	    foreach ($articleAuthorsList as $author) {
                $authorTypeId = NULL;
                if (!is_null($author->getAuthorType()) && $author->getAuthorType()->exists()) {
                    $authorTypeId = $author->getAuthorType()->getId();
                }
	        $metaAuthorsList[] = new MetaAuthor($author->getId(), $authorTypeId);
	    }
	    return $metaAuthorsList;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
	{
        $processesConstraints = array();
        $constraints = array_chunk($p_constraints, 3, true);
        foreach ($constraints as $constraint) {
            if (count($constraint) == 3) {
                $operator = new Operator($constraint[1]);
                $processesConstraints[] = new ComparisonOperation($constraint[0], $operator, $constraint[2]);
            }
        }

        return $processesConstraints;
	}

	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param array $p_order
	 * @return array
	 */
	protected function ProcessOrder(array $p_order)
	{
        if (count($p_order) == 1) {
            $p_order[] = 'asc';
        }
        $state = 1;
        foreach ($p_order as $word) {
            switch ($state) {
            case 1: // reading the order field
                if (array_search(strtolower($word), self::$s_orderFields) === false) {
                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_article_authors, order parameter");
                } else {
                    $orderField = $word;
                    $state = 2;
                }
                break;
            case 2: // reading the order direction
                if (MetaOrder::IsValid($word)) {
                    $order[] = array('field'=>$orderField, 'dir'=>$word);
                } else {
                    CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_article_authors, order parameter");
                }
                $state = 1;
                break;
            }
        }
        if ($state != 1) {
            CampTemplate::singleton()->trigger_error('unexpected end of order parameter in list_article_authors');
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
	    foreach ($p_parameters as $parameter => $value) {
	        $parameter = strtolower($parameter);
	        switch ($parameter) {
	            case 'length':
	            case 'columns':
	            case 'name':
                case 'constraints':
	            case 'order':
	                if ($parameter == 'length' || $parameter == 'columns') {
	                    $intValue = (int)$value;
	                    if ("$intValue" != $value || $intValue < 0) {
	                        CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_article_authors");
	                    }
	                    $parameters[$parameter] = (int) $value;
	                } else {
	                    $parameters[$parameter] = $value;
	                }
	                break;
	            default:
	                CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_article_authors", $p_smarty);
	        }
	    }

        $operator = new Operator('is', 'integer');
        $context = CampTemplate::singleton()->context();
        if (!$context->article->defined) {
        	CampTemplate::singleton()->trigger_error("undefined environment attribute 'Article' in statement list_article_authors");
        	return array();
        }
        $this->m_constraints[] = new ComparisonOperation('article', $operator, $context->article->number);
        if (!$context->language->defined) {
        	CampTemplate::singleton()->trigger_error("undefined environment attribute 'Language' in statement list_article_authors");
        	return array();
        }
        $this->m_constraints[] = new ComparisonOperation('language', $operator, $context->language->number);

		return $parameters;
	}
}

?>
