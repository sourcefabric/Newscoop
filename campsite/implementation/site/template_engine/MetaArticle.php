<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/template_engine/MetaDbObject.php');


/**
 * @package Campsite
 */
final class MetaArticle extends MetaDbObject {
	private $m_articleData = null;

	private $m_state = null;


    public function __construct($p_languageId, $p_articleId)
    {
        $articleObj =& new Article($p_languageId, $p_articleId);

        if (!is_object($articleObj) || !$articleObj->exists()) {
            return false;
        }
        $this->m_dbObject =& $articleObj;
        $this->m_articleData =& new ArticleData($articleObj->getType(),
                                               $articleObj->getArticleNumber(),
                                               $articleObj->getLanguageId());
    } // fn __construct


    final public function __get($p_property)
    {
    	if ($this->m_state == 'type_name_error') {
    		$this->m_state = null;
    		return null;
    	}

    	if ($p_property == 'type' && $this->m_state == null) {
    		$this->m_state = 'type';
    		return $this;
    	}

    	if ($this->m_state == 'type') {
    		if ($this->m_dbObject->getType() != $p_property) {
    			$this->m_state = 'type_name_error';
    		} else {
	    		$this->m_state = null;
    		}
    		return $this;
    	} else {
	    	try {
    			return parent::__get($p_property);
    		} catch (InvalidPropertyException $e) {
    			// do nothing if property not from the article base fields
    		}
    	}

    	try {
	    	return $this->m_articleData->getProperty("F$p_property");
    	} catch (InvalidPropertyException $e) {
    		// do nothing if property not from the article custom fields
    	}
    	throw new InvalidPropertyException(get_class($this->m_dbObject), $p_property);
    } // fn __get

} // class MetaArticle

?>