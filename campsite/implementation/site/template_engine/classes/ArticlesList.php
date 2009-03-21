<?php

require_once('ListObject.php');


/**
 * ArticlesList class
 *
 */
class ArticlesList extends ListObject
{
    private static $s_parameters = array('number'=>array('field'=>'Number', 'type'=>'integer'),
                                         'name'=>array('field'=>'Name', 'type'=>'string'),
                                         'keyword'=>array('field'=>null, 'type'=>'string'),
                                         'onfrontpage'=>array('field'=>'OnFrontPage',
                                                              'type'=>'switch'),
                                         'onsection'=>array('field'=>'OnSection',
                                                            'type'=>'switch'),
                                         'upload_date'=>array('field'=>'UploadDate',
                                                              'type'=>'date'),
                                         'publish_date'=>array('field'=>'PublishDate',
                                                              'type'=>'date'),
                                         'public'=>array('field'=>'Public',
                                                         'type'=>'switch'),
                                         'type'=>array('field'=>'Type',
                                                       'type'=>'string'),
                                         'matchalltopics'=>array('field'=>null,
                                                                 'type'=>'void'),
                                         'matchanytopic'=>array('field'=>null,
                                                                'type'=>'void'),
                                         'topic'=>array('field'=>null,
                                                        'type'=>'topic'),
                                         'reads'=>array('field'=>null, 'type'=>'integer'),
                                         'author'=>array('field'=>null, 'type'=>'string')
                                   );

    private static $s_orderFields = array(
                                          'bynumber',
                                          'byname',
                                          'bydate',
                                          'bycreationdate',
                                          'bypublishdate',
                                          'bypopularity',
                                          'bypublication',
                                          'byissue',
                                          'bysection',
                                          'bylanguage',
                                          'bysectionorder',
                                          'bycomments'
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

	    if ($context->publication->defined && !$p_parameters['ignore_publication']) {
    	    $comparisonOperation = new ComparisonOperation('IdPublication', $operator,
	                                                       $context->publication->identifier);
            $this->m_constraints[] = $comparisonOperation;
	    }
	    if ($context->language->defined && !$p_parameters['ignore_language']) {
	        $comparisonOperation = new ComparisonOperation('IdLanguage', $operator,
	                                                       $context->language->number);
	        $this->m_constraints[] = $comparisonOperation;
	    }
	    if ($context->issue->defined && !$p_parameters['ignore_issue']) {
            $comparisonOperation = new ComparisonOperation('NrIssue', $operator,
                                                           $context->issue->number);
    	    $this->m_constraints[] = $comparisonOperation;
	    }
	    if ($context->section->defined && !$p_parameters['ignore_section']) {
            $comparisonOperation = new ComparisonOperation('NrSection', $operator,
                                                           $context->section->number);
    	    $this->m_constraints[] = $comparisonOperation;
	    }
	    if ($context->topic->defined) {
	        $comparisonOperation = new ComparisonOperation('topic', $operator,
	                                                       $context->topic->identifier);
	        $this->m_constraints[] = $comparisonOperation;
	    }
	    $user = CampTemplate::singleton()->context()->user;
	    if (CampRequest::GetVar('preview') != 'on' || !$user->is_admin) {
	        $comparisonOperation = new ComparisonOperation('published', $operator, 'true');
    	    $this->m_constraints[] = $comparisonOperation;
	    }

	    $articlesList = Article::GetList($this->m_constraints, $this->m_order,
	    $p_start, $p_limit, $p_count);
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
	    $parameters = array();
	    $state = 1;
	    $attribute = null;
	    $operator = null;
	    $value = null;
	    foreach ($p_constraints as $index=>$word) {
	        switch ($state) {
	            case 1: // reading the parameter name
	                $attribute = strtolower($word);
	                if (!array_key_exists($attribute, ArticlesList::$s_parameters)) {
	                    CampTemplate::singleton()->trigger_error("invalid attribute $word in statement list_articles, constraints parameter");
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
	                $type = ArticlesList::$s_parameters[$attribute]['type'];
	                try {
	                    $operator = new Operator($word, $type);
	                }
	                catch (InvalidOperatorException $e) {
    	                CampTemplate::singleton()->trigger_error("invalid operator $word of parameter constraints.$attribute in statement list_articles");
	                    return false;
	                }
	                $state = 3;
	                break;
	            case 3: // reading the value to compare against
	                $type = ArticlesList::$s_parameters[$attribute]['type'];
	                $metaClassName = 'Meta'.ucfirst($type);
	                try {
    	                $valueObj = new $metaClassName($word);
	                } catch (InvalidValueException $e) {
                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_articles");
	                    return false;
	                }
       	            if ($attribute == 'type') {
                        $word = trim($word);
       	                $articleType = new ArticleType($word);
       	                if (!$articleType->exists()) {
	                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_articles");
	                        return false;
       	                }
       	                $value = $word;
       	            } elseif ($attribute == 'topic') {
       	                $topicObj = new Topic($word);
       	                if (!$topicObj->exists()) {
	                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_articles");
	                        return false;
       	                } else {
       	                    $value = $topicObj->getTopicId();
       	                }
       	            } elseif ($attribute == 'author') {
                        if (strtolower($word) == '__current') {
                        	$context = CampTemplate::singleton()->context();
                        	$value = $context->article->author->name;
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
            CampTemplate::singleton()->trigger_error("unexpected end of constraints parameter in list_articles");
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
	                if (array_search(strtolower($word), ArticlesList::$s_orderFields) === false) {
	                    CampTemplate::singleton()->trigger_error("invalid order field $word in list_articles, order parameter");
	                } else {
    	                $orderField = $word;
                        $state = 2;
	                }
	                break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[] = array('field'=>$orderField, 'dir'=>$word);
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_articles, order parameter");
                    }
                    $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_articles");
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
		$parameters['ignore_publication'] = false;
        $parameters['ignore_issue'] = false;
		$parameters['ignore_section'] = false;
        $parameters['ignore_language'] = false;
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
    						CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_articles");
    					}
	    				$parameters[$parameter] = (int)$value;
    				} else {
	    				$parameters[$parameter] = $value;
    				}
    				break;
                case 'ignore_publication':
                case 'ignore_issue':
                case 'ignore_section':
                case 'ignore_language':
                    $value = isset($value) && strtolower($value) != 'false';
                    $parameters[$parameter] = $value;
                    break;
    		    default:
    				CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_articles", $p_smarty);
    		}
    	}
    	return $parameters;
	}
}

?>