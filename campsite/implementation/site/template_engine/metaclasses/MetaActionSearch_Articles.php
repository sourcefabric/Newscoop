<?php

define('ACTION_SEARCH_ARTICLES_ERR_NO_KEYWORD', 'action_search_articles_err_no_keyword');
define('ACTION_SEARCH_ARTICLES_ERR_INVALID_SCOPE', 'action_search_articles_err_invalid_scope');


class MetaActionSearch_Articles extends MetaAction
{
    const SEARCH_LEVEL_MULTIPLE_PUBLICATION = 0;
    
    const SEARCH_LEVEL_PUBLICATION = 1;
    
    const SEARCH_LEVEL_ISSUE = 2;
    
    const SEARCH_LEVEL_SECTION = 3;
    
    const DEFAULT_SEARCH_LEVEL = MetaActionSearch_Articles::SEARCH_LEVEL_PUBLICATION;
    
    /**
     * Stores the total number of articles that matched the search criteria.
     *
     * @var int
     */
    private $m_totalCount = null;


    /**
     * Reads the input parameters and sets up the articles search action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'search_articles';
        if (!isset($p_input['f_search_keywords'])) {
            $this->m_error = new PEAR_Error('No keyword was filled in.',
            ACTION_SEARCH_ARTICLES_ERR_NO_KEYWORD);
            return;
        }
        $this->m_properties['search_phrase'] = $p_input['f_search_keywords'];
        $this->m_properties['search_keywords'] = preg_split('/[\s,.-]/', $p_input['f_search_keywords']);
        $this->m_properties['search_results'] = 'getSearchResults';

        $this->m_properties['match_all'] = isset($p_input['f_match_all'])
        && strtolower($p_input['f_match_all']) == 'on';

        if (isset($p_input['f_search_level'])) {
            if ($p_input['f_search_level'] < MetaActionSearch_Articles::SEARCH_LEVEL_MULTIPLE_PUBLICATION
            || $p_input['f_search_level'] > MetaActionSearch_Articles::SEARCH_LEVEL_SECTION) {
                $p_input['f_search_level'] = MetaActionSearch_Articles::DEFAULT_SEARCH_LEVEL;
            }
            $this->m_properties['search_level'] = $p_input['f_search_level'];
        } else {
            $this->m_properties['search_level'] = MetaActionSearch_Articles::DEFAULT_SEARCH_LEVEL;
        }

        if (isset($p_input['f_search_issue'])) {
        	$this->m_properties['search_issue'] = (int)$p_input['f_search_issue'];
        } else {
        	$this->m_properties['search_issue'] = 0;
        }

        if (isset($p_input['f_search_section'])) {
            $this->m_properties['search_section'] = (int)$p_input['f_search_section'];
        } else {
            $this->m_properties['search_section'] = 0;
        }

        if (isset($p_input['f_search_start_date'])) {
        	$this->m_properties['start_date'] = $p_input['f_search_start_date'];
        } else {
        	$this->m_properties['start_date'] = null;
        }

        if (isset($p_input['f_search_end_date'])) {
            $this->m_properties['end_date'] = $p_input['f_search_end_date'];
        } else {
            $this->m_properties['end_date'] = null;
        }

        if (isset($p_input['f_search_topic'])
        && $p_input['f_search_topic'] > 0) {
            $this->m_properties['topic_id'] = $p_input['f_search_topic'];
        } else {
            $this->m_properties['topic_id'] = null;
        }

        $this->m_properties['submit_button'] = $p_input['f_search_articles'];

        $this->m_properties['scope'] = 'index';
        if (isset($p_input['f_search_scope'])) {
        	$searchScope = strtolower($p_input['f_search_scope']);
        	switch ($searchScope) {
        		case 'keywords':
        		case 'content':
        			$this->m_properties['scope'] = 'index';
        			break;
        		case 'title':
        		case 'author':
        			$this->m_properties['scope'] = $searchScope;
        			break;
        		default:
                    $this->m_error = new PEAR_Error('Invalid search scope specified.',
                    ACTION_SEARCH_ARTICLES_ERR_INVALID_SCOPE);
        			return;
        	}
        }

        $this->m_error = ACTION_OK;
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        if (isset($p_input['tpl'])) {
            $template = new MetaTemplate($p_input['tpl']);
            if ($template->defined()) {
                $this->m_properties['template'] = $template;
            } else {
                $this->m_properties['template'] = $p_context->template;
            }
        } else {
            $this->m_properties['template'] = $p_context->template;
        }
        
        $fields = array('f_search_articles', 'f_match_all', 'f_search_level',
                        'f_search_keywords', 'f_search_issue', 'f_search_section',
                        'f_search_start_date', 'f_search_end_date', 'f_search_topic_id',
                        'f_search_scope');
        foreach ($fields as $field) {
            $p_context->default_url->reset_parameter($field);
            $p_context->url->reset_parameter($field);
        }

	    $operator = new Operator('is', 'integer');
	    $this->m_properties['constraints'] = array();
	    if ($this->m_properties['search_level'] >= MetaActionSearch_Articles::SEARCH_LEVEL_PUBLICATION
	    && $p_context->publication->defined) {
	        $this->m_properties['constraints'][] = new ComparisonOperation('Articles.IdPublication', $operator,
	                                                         $p_context->publication->identifier);
	    }
	    if ($this->m_properties['search_level'] >= MetaActionSearch_Articles::SEARCH_LEVEL_ISSUE
	    && $p_context->issue->defined && $this->m_properties['search_issue'] == 0) {
	        $this->m_properties['constraints'][] = new ComparisonOperation('Articles.NrIssue', $operator,
	                                                         $p_context->issue->number);
	    }
	    if ($this->m_properties['search_level'] >= MetaActionSearch_Articles::SEARCH_LEVEL_SECTION
	    && $p_context->section->defined && $this->m_properties['search_section'] == 0) {
	        $this->m_properties['constraints'][] = new ComparisonOperation('Articles.NrSection', $operator,
	                                                         $p_context->section->number);
	    }
	    if ($this->m_properties['search_issue'] != 0) {
	    	$this->m_properties['constraints'][] = new ComparisonOperation('Articles.NrIssue', $operator,
                                                             $this->m_properties['search_issue']);
	    }
        if ($this->m_properties['search_section'] != 0) {
            $this->m_properties['constraints'][] = new ComparisonOperation('Articles.NrSection', $operator,
                                                             $this->m_properties['search_section']);
        }
	    if (!empty($this->m_properties['start_date'])) {
            $startDateOperator = new Operator('greater_equal', 'date');
	    	$this->m_properties['constraints'][] = new ComparisonOperation('Articles.PublishDate', $startDateOperator,
	    	                                                 $this->m_properties['start_date']);
	    }
	    if (!empty($this->m_properties['end_date'])) {
            $endDateOperator = new Operator('smaller_equal', 'date');
	    	$this->m_properties['constraints'][] = new ComparisonOperation('Articles.PublishDate', $endDateOperator,
                                                             $this->m_properties['end_date']);
        }
        if (!empty($this->m_properties['topic_id'])) {
            $this->m_properties['constraints'][] = new ComparisonOperation('ArticleTopics.TopicId', $operator,
                                                             $this->m_properties['topic_id']);
        }
        return true;
    }


    /**
     * Returns the value of the given property; throws
     * InvalidPropertyHandlerException if the property didn't exist.
     *
     * @param string $p_property
     * @return mixed
     */
    public function __get($p_property)
    {
        $p_property = MetaAction::TranslateProperty($p_property);
        if ($p_property == 'results_count') {
        	if (!empty($this->m_totalCount)) {
        		return $this->m_totalCount;
        	}
            if ($this->m_properties['scope'] == 'index') {
                Article::SearchByKeyword($this->m_properties['search_keywords'],
                                         $this->m_properties['match_all'],
                                         $this->m_properties['constraints'],
                                         array(), 0, 0,
                                         $this->m_totalCount, true);
            } else {
                Article::SearchByField($this->m_properties['search_keywords'],
                                       $this->m_properties['scope'],
                                       $this->m_properties['match_all'],
                                       $this->m_properties['constraints'],
                                       array(), 0, 0,
                                       $this->m_totalCount, true);
            }
            return $this->m_totalCount;
        }
        return parent::__get($p_property);
    }
}

?>