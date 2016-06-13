<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ContextBoxArticle.php');
/**
 * @package Campsite
 */

Class ContextBox extends DatabaseObject
{
    var $m_dbTableName = 'context_boxes';
    var $m_keyColumnNames = array('id');
    var $m_keyIsAutoIncrement = true;
    var $m_columnNames = array('id', 'fk_article_no');

    public function __construct($p_id = null, $p_article_no = null)
    {
    	parent::__construct($this->m_columnNames);
        if (is_numeric($p_id) && $p_id > 0) {
            $this->m_data['id'] = $p_id;
            if ($this->keyValuesExist()) {
                $this->fetch();
            }
        } else {
	        if(is_numeric($p_article_no) && $p_article_no > 0) {
	            $this->m_data['fk_article_no'] = $p_article_no;
                $this->m_keyColumnNames = array('fk_article_no');
                $this->fetch();
	            $this->m_keyColumnNames = array('id');
	        }
        }
        if(!$this->exists()) {
        	$this->create($p_article_no);
        }
    }

    public function create($p_article_no = null)
    {
        if (empty($p_article_no)) {
            return false;
        }
        $columns['fk_article_no'] = (int) $p_article_no;
        $result = parent::create($columns);
        return $result;
    }

    public function getId()
    {
    	return $this->m_data['id'];
    }

    public function getArticlesList()
    {
        return ContextBoxArticle::GetList(array('context_box' => $this->getId()), null, 0, 0, $p_count, FALSE);
    }


	/**
	 * Remove the context box for the given article.
	 * @param int $articleNumber
	 * @return void
	 */
    public static function OnArticleDelete($articleNumber)
    {
		global $g_ado_db;

		$articleNumber = (int)$articleNumber;
		if ($articleNumber < 1) {
		    return;
		}

		$queryStr = 'SELECT * FROM context_boxes'
		. " WHERE fk_article_no = '$articleNumber'";
		$boxes = $g_ado_db->GetAll($queryStr);
		foreach ($boxes as $box) {
		    ContextBoxArticle::OnContextBoxDelete($box['id']);
		}

		$queryStr = 'DELETE FROM context_boxes'
					." WHERE fk_article_no = '$articleNumber'";
		$g_ado_db->Execute($queryStr);
    }
}
