<?php

define('ACTION_SEARCH_ARTICLES_ERR_NO_KEYWORD', 'action_search_articles_err_no_keyword');


class MetaActionSearchArticles extends MetaAction
{
    const SEARCH_LEVEL_MULTIPLE_PUBLICATION = 0;
    
    const SEARCH_LEVEL_PUBLICATION = 1;
    
    const SEARCH_LEVEL_ISSUE = 2;
    
    const SEARCH_LEVEL_SECTION = 3;
    
    const DEFAULT_SEARCH_LEVEL = SEARCH_LEVEL_PUBLICATION;
    
    /**
     * Stores the array of articles that matched the search criteria.
     *
     * @var array
     */
    private $m_articlesList = array();

    /**
     * Stores the total number of articles that matched the search criteria.
     *
     * @var int
     */
    private $m_totalCount;


    /**
     * Reads the input parameters and sets up the login action.
     *
     * @param array $p_input
     */
    public function __construct(array $p_input)
    {
        $this->m_defined = true;
        $this->m_name = 'searcharticles';
        if (!isset($p_input['f_search_keywords'])) {
            $this->m_error = new PEAR_Error('No keyword was filled in.',
            ACTION_SEARCH_ARTICLES_ERR_NO_KEYWORD);
            return;
        }
        $this->m_properties['search_phrase'] = $p_input['f_search_keywords'];
        $this->m_properties['search_keywords'] = preg_split('/[\s,.-]/', $p_input['f_search_keywords']);

        if (isset($p_input['tpl'])) {
            $template = new MetaTemplate($p_input['tpl']);
            if ($template->defined()) {
                $this->m_properties['template'] = $template;
            } else {
                $this->m_properties['template'] = CampTemplate::singleton()->context()->template;
            }
        } else {
            $this->m_properties['template'] = CampTemplate::singleton()->context()->template;
        }

        $this->m_properties['match_all'] = isset($p_input['f_match_all'])
        && strtolower($p_input['f_match_all']) == true ? 'true' : 'false';

        if (isset($p_input['f_search_level'])) {
            if ($p_input['f_search_level'] < MetaActionSearchArticles::SEARCH_LEVEL_MULTIPLE_PUBLICATION
            || $p_input['f_search_level'] > MetaActionSearchArticles::SEARCH_LEVEL_SECTION) {
                $p_input['f_search_level'] = MetaActionSearchArticles::DEFAULT_SEARCH_LEVEL;
            }
            $this->m_properties['search_level'] = $p_input['f_search_level'];
        } else {
            $this->m_properties['search_level'] = MetaActionSearchArticles::DEFAULT_SEARCH_LEVEL;
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
        return true;
    }
}

?>