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

class BlogsList extends ListObject 
{   
    public static $s_parameters = array('identifier' => array('field' => 'blog_id', 'type' => 'integer'),
                                        'language_id' => array('field' => 'fk_language_id', 'type' => 'integer'),
                                        'user_id' => array('field' => 'fk_user_id', 'type' => 'integer'),
                                        'name' => array('field' => 'title', 'type' => 'string'),
                                        'title' => array('field' => 'title', 'type' => 'string'),
                                        'published' => array('field' => 'published', 'type' => 'datetime'),
                                        'published_year' => array('field' => 'YEAR(published)', 'type' => 'integer'),
                                        'published_month' => array('field' => 'MONTH(published)', 'type' => 'integer'),
                                        'published_mday' => array('field' => 'DAYOFMONTH(published)', 'type' => 'integer'),
                                        'published_wday' => array('field' => 'DAYOFWEEK(published)', 'type' => 'integer'),
                                        'status' => array('field' => 'status', 'type' => 'string'),
                                        'admin_status' => array('field' => 'admin_status', 'type' => 'string'),
                                        'entries_online' => array('field' => 'entries_online', 'type' => 'integer'),
                                        'entries_offline' => array('field' => 'entries_offline', 'type' => 'integer'),
                                        'entries' => array('field' => 'entries_online + entries_offline', 'type' => 'integer'),
                                        'feature' => array('field' => 'feature', 'type' => 'string'),
                                        'matchalltopics'=>array('field' => null, 'type'=>'void'),
                                        'matchanytopic'=>array('field' => null, 'type'=>'void'),
                                        'topic'=>array('field' => null,'type'=>'topic'),
                               );
                                   
    private static $s_orderFields = array(
                                      'byidentifier',
                                      'bylanguage_id',
                                      'byuser_id',
                                      'byname',
                                      'bytitle',
                                      'bypublished',
                                      'bypublished_year',
                                      'bypublished_month',
                                      'bypublished_mday',
                                      'bypublished_wday',
                                      'bystatus',
                                      'byadmin_status',
                                      'byentries_online',
                                      'byentries_offline',
                                      'byentries',
                                      'byentries',
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
    	    $overwritten = false;
    	    
    	    if ($context->language->defined) {
        	    $comparisonOperation = new ComparisonOperation('fk_language_id', $operator,
    	                                                       $context->language->number);
                $this->m_constraints[] = $comparisonOperation;
    	    }
    	    if ($context->topic->defined) {
    	        $comparisonOperation = new ComparisonOperation('topic', $operator,
    	                                                       $context->topic->identifier);
    	        $this->m_constraints[] = $comparisonOperation;
    	    }
	    }
	    $blogsList = Blog::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit, $p_count);
        $metaBlogsList = array();
	    foreach ($blogsList as $blog) {
	        $metaBlogsList[] = new MetaBlog($blog->getId());
	    }
	    return $metaBlogsList;
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
	    foreach ($p_constraints as $index=>$word) {
	        switch ($state) {
	            case 1: // reading the parameter name
	                $attribute = strtolower($word);
	                if (!array_key_exists($attribute, self::$s_parameters)) {
	                    CampTemplate::singleton()->trigger_error("invalid attribute $word in statement list_blogs, constraints parameter");
	                    return false;
	                }
	                if ($attribute == 'keyword') {
	                    $operator = new Operator('is', 'string');
	                    $state = 3;
	                } elseif ($attribute == 'matchalltopics' || $attribute == 'matchanytopic') {
	                    if ($attribute == 'matchalltopics') {
	                        $operator = new Operator('is', 'boolean');
	                        $comparisonOperation = new ComparisonOperation($attribute, $operator, 'true');
	                        $parameters[] = $comparisonOperation;
	                    }
	                    $state = 1;
	                } else {
                        $state = 2;
	                }
	                if ($attribute == 'onfrontpage' || $attribute == 'onsection') {
	                    if (($index + 1) < count($p_constraints)) {
	                        try {
	                            $operator = new Operator($p_constraints[$index+1], 'switch');
	                        }
	                        catch (InvalidOperatorException $e) {
        	                    $operator = new Operator('is', 'switch');
        	                    $comparisonOperation = new ComparisonOperation($attribute, $operator, 'on');
                	            $parameters[] = $comparisonOperation;
                	            $state = 1;
	                        }
	                    } else {
    	                    $operator = new Operator('is', 'switch');
                            $comparisonOperation = new ComparisonOperation($attribute, $operator, 'on');
                            $parameters[] = $comparisonOperation;
                            $state = 1;
	                    }
	                }
	                break;
	            case 2: // reading the operator
	                $type = self::$s_parameters[$attribute]['type'];
	                try {
	                    $operator = new Operator($word, $type);
	                }
	                catch (InvalidOperatorException $e) {
    	                CampTemplate::singleton()->trigger_error("invalid operator $word of parameter constraints.$attribute in statement list_blogs");
	                    return false;
	                }
	                $state = 3;
	                break;
	            case 3: // reading the value to compare against
	                $type = self::$s_parameters[$attribute]['type'];
	                $metaClassName = 'Meta'.ucfirst($type);
	                try {
    	                $valueObj = new $metaClassName($word);
	                } catch (InvalidValueException $e) {
                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_blogs");
	                    return false;
	                }
       	            if ($attribute == 'type') {
                        $word = trim($word);
       	                $blogType = new BlogType($word);
       	                if (!$blogType->exists()) {
	                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_blogs");
	                        return false;
       	                }
       	                $value = $word;
       	            } elseif ($attribute == 'topic') {
       	                $topicObj = new Topic($word);
       	                if (!$topicObj->exists()) {
	                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_blogs");
	                        return false;
       	                } else {
       	                    $value = $topicObj->getTopicId();
       	                }
       	            } elseif ($attribute == 'author') {
                        if (strtolower($word) == '__current') {
                        	$context = CampTemplate::singleton()->context();
                        	$value = $context->blog->author->name;
                        } else {
                        	$value = $word;
                        }
       	            } else {
       	                $value = $word;
       	            }
       	            $comparisonOperation = new ComparisonOperation($attribute, $operator, $value);
    	            $parameters[] = $comparisonOperation;
	                $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of constraints parameter in list_blogs");
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
	    if (!is_array($p_order)) {
	        return null;
	    }

	    $order = array();
	    $state = 1;
	    foreach ($p_order as $word) {
	        switch ($state) {
                case 1: // reading the order field
	                if (array_search(strtolower($word), BlogsList::$s_orderFields) === false) {
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