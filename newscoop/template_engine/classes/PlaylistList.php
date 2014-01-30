<?php

require_once('ListObject.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/CampCache.php');


/**
 * ArticlesList class
 *
 */
class PlaylistList extends ListObject
{
    private static $s_parameters = array
    (
    	'id' => array( 'field' => 'id_playlist', 'type'=>'integer'),
		'name' => array( 'field' => 'name', 'type'=>'string' )
    );
    private static $s_orderFields = array();
    private static $s_articleTypes = null;
    private static $s_dynamicFields = null;

    const CONSTRAINT_ATTRIBUTE_NAME = 1;
    const CONSTRAINT_DYNAMIC_FIELD = 4;
    const CONSTRAINT_OPERATOR = 2;
    const CONSTRAINT_VALUE = 3;

    private $m_ignoreIssue = false;
    private $m_ignoreSection = false;


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
	    $doctrine = Zend_Registry::get('container')->getService('doctrine');
        if (!$doctrine) {
            return false;
        }

        $repo = $doctrine->getManager()->getRepository('Newscoop\Entity\Playlist');
        /* @var $repo \Newscoop\Entity\Repository\PlaylistRepository */

        // get playlist
        if (isset($p_parameters['name']) && trim($p_parameters['name'])) {
            $playlist = current($repo->findBy(array( "name" => $p_parameters['name'] )));
        }
        if (isset($p_parameters['id']) && trim($p_parameters['id'])!="") {
            $playlist = $repo->find($p_parameters['id']);
        }
        if (!($playlist instanceof \Newscoop\Entity\Playlist)) {
            return array();
        }
        $length = null;
        if (isset($p_parameters['length']) && trim($p_parameters['length'])!="") {
            $length = $p_parameters['length'];
        }
	    $start = null;
        if (isset($p_parameters['start']) && trim($p_parameters['start'])!="") {
            $start = $p_parameters['start'];
        }

        $context = CampTemplate::singleton()->context();

        $langRepo = $doctrine->getManager()->getRepository('Newscoop\Entity\Language');
        /* @var $langRepo \Newscoop\Entity\Repository\LanguageRepository */
        $languageId = $context->language->number;
        $lang = $langRepo->find($context->language->number);

        $preview = $context->preview;

        $articlesList = $repo->articles($playlist, $lang, false, $length, $start, !$preview);

        $metaArticlesList = array();
	    foreach ($articlesList as $article) {
	        $metaArticle = new MetaArticle($lang->getId(), $article['articleId']);
	        if ($metaArticle->defined()
	        && ($metaArticle->is_published
	            || ($preview && $metaArticle->section->defined() && $metaArticle->issue->defined())
	           )
	        ) {
	            $metaArticlesList[] = $metaArticle;
	        }
	    }

	    $p_count = $repo->articlesCount($playlist, $lang, !$preview);
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
	    return $p_constraints;
	}


	/**
	 * Processes order constraints passed in an array.
	 *
	 * @param array $p_order
	 * @return array
	 */
	protected function ProcessOrder(array $p_order)
	{
	    return $p_order;
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
	    foreach ($p_parameters as $parameter=>$value)
	    {
	        switch (($parameter = strtolower($parameter)))
	        {
	            case 'columns':
	            case 'start' :
	            case 'length' :
                    $intValue = (int)$value;
    				if ("$intValue" != $value || $intValue < 0) {
    				    CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_articles");
                    }
	    			$parameters[$parameter] = (int)$value;
	                break;
	            case 'name' :
	                $parameters[$parameter] = (string)$value;
	                break;
	            case 'id' :
	                $parameters[$parameter] = (int)$value;
	                break;
	        }
	    }
		return $parameters;
	}
}

