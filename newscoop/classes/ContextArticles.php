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
/**
 * @package Campsite
 */

Class ContextArticles extends DatabaseObject {
    var $m_dbTableName = 'context_articles';


    var $m_columnNames = array('fk_context_id', 'fk_article_no');

    public function __construct($p_context_id = null) {
        parent::__construct($this->m_columnNames);
    }

    public function saveList($p_context_id, $p_article_no_array) {
        $this->removeList($p_context_id);
        $this->insertList($p_context_id, $p_article_no_array);
    }

    public function removeList($p_context_id) {
    	Global $g_ado_db;
        $queryStr = 'DELETE FROM ' . $this->m_dbTableName
                    .' WHERE fk_context_id=' . $p_context_id.'';
        $g_ado_db->Execute($queryStr);
        $wasDeleted = ($g_ado_db->Affected_Rows());
        return $wasDeleted;
    }

    public function insertList($p_context_id, $p_article_no_array) {
    	Global $g_ado_db;
    	foreach($p_article_no_array as $p_article_no) {
    		$queryStr = 'INSERT INTO ' . $this->m_dbTableName
    		          . ' VALUES ('.$p_context_id.','.$p_article_no.')';
    		$g_ado_db->Execute($queryStr);
    	}
    }

    public function getList($p_context_id) {
    	Global $g_ado_db;
    	$returnArray = array();
    	$queryStr = '
    	   SELECT fk_article_no FROM ' . $this->m_dbTableName .
    	   ' WHERE fk_context_id='.$p_context_id
    	;
        $rows = $g_ado_db->GetAll($queryStr);
        if(is_array($rows)) {
        	foreach($rows as $row) {
        		$returnArray[] = $row['fk_article_no'];
        	}
        }
        return array_reverse($returnArray);
    }

}