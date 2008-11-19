<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <sebastian.goebel@web.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

class BlogCommentsList extends ListObject 
{   
    public static $s_parameters = array('identifier' => array('field' => 'comment_id', 'type' => 'integer'),
                                        'entry_id' => array('field' => 'fk_entry_id', 'type' => 'integer'),
                                        'blog_id' => array('field' => 'fk_blog_id', 'type' => 'integer'),
                                        'language_id' => array('field' => 'fk_language_id', 'type' => 'integer'),
                                        'user_id' => array('field' => 'fk_user_id', 'type' => 'integer'),
                                        'published' => array('field' => 'published', 'type' => 'datetime'),
                                        'published_year' => array('field' => 'YEAR(published)', 'type' => 'integer'),
                                        'published_month' => array('field' => 'MONTH(published)', 'type' => 'integer'),
                                        'published_mday' => array('field' => 'DAYOFMONTH(published)', 'type' => 'integer'),
                                        'published_wday' => array('field' => 'DAYOFWEEK(published)', 'type' => 'integer'),
                                        'name' => array('field' => 'title', 'type' => 'string'),
                                        'title' => array('field' => 'title', 'type' => 'string'),
                                        'content' => array('field' => 'content', 'type' => 'string'),
                                        'mood' => array('field' => 'mood', 'type' => 'string'),
                                        'status' => array('field' => 'status', 'type' => 'string'),
                                        'admin_status' => array('field' => 'admin_status', 'type' => 'string'),
                                        'feature' => array('field' => 'feature', 'type' => 'string'),
                               );
                                   
    private static $s_orderFields = array(
                                      'byidentifier',
                                      'byentry_id',
                                      'byblog_id',
                                      'byuser_id',
                                      'bypublished',
                                      'bypublished_year',
                                      'bypublished_month',
                                      'bypublished_mday',
                                      'bypublished_wday',
                                      'byname',
                                      'bytitle',
                                      'bycontent',
                                      'bymood',
                                      'bystatus',
                                      'byadmin_status',
                                      'byfeature',
                                );
                                   
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
	protected function CreateList($p_start = 0, $p_limit = 0, array $p_parameters, &$p_count)
	{
	    if (!defined('PLUGIN_BLOG_ADMIN_MODE')) {
    	    $operator = new Operator('is', 'integer');
    	    $context = CampTemplate::singleton()->context();
    	    
    	    if ($context->blogentry->defined) {
        	    $comparisonOperation = new ComparisonOperation('entry_id', $operator, $context->blogentry->identifier);
                $this->m_constraints[] = $comparisonOperation;
    	    } elseif ($context->blog->defined) {
        	    $comparisonOperation = new ComparisonOperation('blog_id', $operator, $context->blog->identifier);
                $this->m_constraints[] = $comparisonOperation;
    	    }
	    }
	    
	    $BlogCommentsList = BlogComment::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit, $p_count);
        $metaBlogCommentsList = array();
	    foreach ($BlogCommentsList as $blogComment) {
	        $metaBlogCommentsList[] = new MetaBlogComment($blogComment->getId());
	    }
	    return $metaBlogCommentsList;
	}

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
	{
	    if (!is_array($p_constraints)) {
	        return null;
	    }

	    $parameters = array();
	    $state = 1;
	    $attribute = null;
	    $operator = null;
	    $value = null;
	    foreach ($p_constraints as $word) {
	        switch ($state) {
	            case 1: // reading the parameter name
	                if (!array_key_exists($word, BlogCommentsList::$s_parameters)) {
	                    CampTemplate::singleton()->trigger_error("invalid attribute $word in list_blogs, constraints parameter");
	                    break;
	                }
	                $attribute = $word;
	                $state = 2;
	                break;
	            case 2: // reading the operator
	                $type = BlogCommentsList::$s_parameters[$attribute]['type'];
	                try {
	                    $operator = new Operator($word, $type);
	                }
	                catch (InvalidOperatorException $e) {
	                    CampTemplate::singleton()->trigger_error("invalid operator $word for attribute $attribute in list_blogs, constraints parameter");
	                }
	                $state = 3;
	                break;
	            case 3: // reading the value to compare against
	                $type = BlogCommentsList::$s_parameters[$attribute]['type'];
	                $metaClassName = 'Meta'.strtoupper($type[0]).strtolower(substr($type, 1));
	                try {
	                    $value = new $metaClassName($word);
    	                $value = $word;
       	                $comparisonOperation = new ComparisonOperation($attribute, $operator, $value);
    	                $parameters[] = $comparisonOperation;
	                } catch (InvalidValueException $e) {
	                    CampTemplate::singleton()->trigger_error("invalid value $word of attribute $attribute in list_blogs, constraints parameter");
	                }
	                $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of constraints parameter in list_blogs");
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
	    if (!is_array($p_order)) {
	        return null;
	    }

	    $order = array();
	    $state = 1;
	    foreach ($p_order as $word) {
	        switch ($state) {
                case 1: // reading the order field
	                if (array_search(strtolower($word), BlogCommentsList::$s_orderFields) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_blogs, order parameter");
	                } else {
    	                $orderField = $word;
	                }
	                $state = 2;
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[$orderField] = $word;
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_blogs, order parameter");
                    }
                    $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_blogs");
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
    			case 'constraints':
    			case 'order':
    				if ($parameter == 'length' || $parameter == 'columns') {
    					$intValue = (int)$value;
    					if ("$intValue" != $value || $intValue < 0) {
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_blogs");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
    			default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_blogs", $p_smarty);
    		}
    	}
    	 
    	return $parameters;
	}
	

	/**
     * Overloaded method call to give access to the list properties.
     *
     * @param string $p_element - the property name
     * @return mix - the property value
     */
	public function __get($p_property)
	{
	    return parent::__get($p_property); 
	}
}

?>