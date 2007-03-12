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
require_once($g_documentRoot.'/classes/Exceptions.php');


/**
 * @package Campsite
 */
final class MetaArticle {
    //
    private $m_data = null;
    //
    private $m_instance = false;
    //
    private $m_baseFields = array(
                                  'Number',
                                  'Name',
                                  'Type',
                                  'PublishDate',
                                  'UploadDate',
                                  'Keywords'
                                  );
    public $m_customFields = null;


    public function __construct($p_languageId, $p_articleId)
    {
        $articleObj = new Article($p_languageId, $p_articleId);

        if (!is_object($articleObj) || !$articleObj->exists()) {
            return false;
        }
        foreach ($articleObj->m_data as $key => $value) {
            if (in_array($key, $this->m_baseFields)) {
                $this->m_data[$key] = $value;
            }
        }
        $articleDataObj = new ArticleData($articleObj->getType(),
                                          $articleObj->getArticleNumber(),
                                          $articleObj->getLanguageId());
        foreach ($articleDataObj->m_data as $key => $value) {
            if (substr($key, 0, 1) == 'F') {
                $customFieldName = substr($key, 1, strlen($key) - 1);
                $this->m_data[$customFieldName] = $value;
                $this->m_customFields[] = $customFieldName;
            }
        }
        $this->m_instance = true;
    } // fn __construct


    public function __get($p_property)
    {
        if (!is_array($this->m_data)) {
            return false;
        }
        if (!array_key_exists($p_property, $this->m_data)) {
            return false;
        }

        return $this->m_data[$p_property];
    } // fn __get


    public function __set($p_property, $p_value)
    {
        throw new InvalidFunctionException(get_class($this), '__set');
    } // fn __set


    public function defined()
    {
        return $this->m_instance;
    } // fn defined

} // class MetaArticle

?>