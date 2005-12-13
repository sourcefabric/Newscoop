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
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/Log.php');

/**
 * @package Campsite
 */
class Section extends DatabaseObject {
	var $m_dbTableName = 'Sections';
	var $m_keyColumnNames = array('IdPublication', 
								  'NrIssue',
								  'IdLanguage',
								  'Number');
	var $m_columnNames = array(
		'IdPublication',
		'NrIssue',
		'IdLanguage',
		'Number',
		'Name',
		'ShortName',
		'SectionTplId',
		'ArticleTplId');
	
	/**
	 * A section is a part of an issue.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_languageId
	 * @param int $p_sectionNumber
	 */
	function Section($p_publicationId = null, $p_issueId = null, 
	                 $p_languageId = null, $p_sectionNumber = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdPublication'] = $p_publicationId;
		$this->m_data['NrIssue'] = $p_issueId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['Number'] = $p_sectionNumber;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // fn Section

	
	/**
	 * Create a new Section.
	 * @param string $p_name
	 * @param string $p_shortName
	 */
	function create($p_name, $p_shortName, $p_columns = null) {
	    if (!is_array($p_columns)) {
	        $p_columns = array();
	    }
	    $p_columns['Name'] = $p_name;
	    $p_columns['ShortName'] = $p_shortName;
	    $success = parent::create($p_columns);
	    if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
		    $logtext = getGS('Section $1 added. (Issue: $2, Publication: $3)',
		        $this->m_data['Name']." (".$this->m_data['Number'].")", 
		        $this->m_data['NrIssue'], 
		        $this->m_data['IdPublication']); 
		    Log::Message($logtext, null, 21);
	    }
	    return $success;
	} // fn create
	

	/**
	 * Copy the section to the given issue.  The issue can be the same as
	 * the current issue.  All articles will be copied to the new section.
	 *
	 * @param int $p_destPublicationId
	 *     The destination publication ID.
	 * @param int $p_destIssueId
	 *     The destination issue ID.
	 * @param int $p_destIssueLanguageId 
	 *     (optional) The destination issue language ID.  If not given, 
	 *     it will use the language ID of this section.
	 * @param int $p_destSectionId
	 *     (optional) The destination section ID.  If not given, a new 
	 *     section will be created.
	 * @param boolean $p_copyArticles
	 *     (optional) If set to true, all articles will be copied to the
	 *     destination section.
	 * @return Section
	 *     The new Section object.
	 */
	function copy($p_destPublicationId, $p_destIssueId, $p_destIssueLanguageId = null, 
	              $p_destSectionId = null, $p_copyArticles = true) {
    	if (is_null($p_destIssueLanguageId)) {
    	   $p_destIssueLanguageId = $this->m_data['IdLanguage'];   
    	}
    	if (is_null($p_destSectionId)) {
    	    $p_destSectionId = $this->m_data['Number'];
    	}
    	$dstSectionObj =& new Section($p_destPublicationId, $p_destIssueId, 
    	                              $p_destIssueLanguageId, $p_destSectionId);
    	// If source issue and destination issue are the same
    	if ( ($this->m_data['IdPublication'] == $p_destPublicationId) 
    	      && ($this->m_data['NrIssue'] == $p_destIssueId)
    	      && ($this->m_data['IdLanguage'] == $p_destIssueLanguageId) ) {
    		$shortName = $p_destSectionId;
    		$sectionName = $this->getName() . " (duplicate)";
    	} else {
    		$shortName = $this->getUrlName();
    		$sectionName = $this->getName();
    	}
    	$dstSectionCols = array();
   		$dstSectionCols['SectionTplId'] = $this->m_data['SectionTplId'];
   		$dstSectionCols['ArticleTplId'] = $this->m_data['ArticleTplId'];
    	
   		// Create the section if it doesnt exist yet.
    	if (!$dstSectionObj->exists()) {
    		$dstSectionObj->create($sectionName, $shortName, $dstSectionCols);
    	}
    	
    	// Copy all the articles.
    	if ($p_copyArticles) {
        	$srcSectionArticles = Article::GetArticles($this->m_data['IdPublication'], 
                                                       $this->m_data['NrIssue'], 
                                                       $this->m_data['Number']);
            $copiedArticles = array();
        	foreach ($srcSectionArticles as $articleObj) {
        	    if (!in_array($articleObj->getArticleNumber(), $copiedArticles)) {
            		$tmpCopiedArticles = $articleObj->copy($p_destPublicationId, 
                        $p_destIssueId, $p_destSectionId, null, true);
                    $copiedArticles[] = $articleObj->getArticleNumber();
        	    }
        	}
    	}
    	    	
    	return $dstSectionObj;
	} // fn copy

	
	/**
	 * Delete the section, and optionally the articles.
	 * @param boolean $p_deleteArticles
	 * @return boolean
	 */
	function delete($p_deleteArticles = false) 
	{
	    $numArticlesDeleted = 0;
	    if ($p_deleteArticles) {
	        $articles = Article::GetArticles($this->m_data['IdPublication'], 
	                                          $this->m_data['NrIssue'],
	                                          $this->m_data['Number']);
	        $numArticlesDeleted = count($articles);
            foreach ($articles as $deleteMe) {
                $deleteMe->delete();
            }
	    }
	    $success = parent::delete();
	    if ($success) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
	        $logtext = getGS('Section $1 deleted. (Issue: $2, Publication: $3)',
		        $this->m_data['Name']." (".$this->m_data['Number'].")",
		        $this->m_data['NrIssue'],
		        $this->m_data['IdPublication']);
		    Log::Message($logtext, null, 22);
	    }
	    return $numArticlesDeleted;
	} // fn delete
	
		
	/**
	 * @return int
	 */
	function getPublicationId() 
	{
		return $this->getProperty('IdPublication');
	} // fn getPublicationId
	
	
	/**
	 * @return int
	 */
	function getIssueNumber() 
	{
		return $this->getProperty('NrIssue');
	} // fn getIssueNumber
	
	
	/**
	 * @return int
	 */
	function getLanguageId() 
	{
		return $this->getProperty('IdLanguage');
	} // fn getLanguageId
	
	
	/**
	 * @return int
	 */
	function getSectionNumber() 
	{
		return $this->getProperty('Number');
	} // fn getSectionNumber

	
	/**
	 * @return string
	 */ 
	function getName() 
	{
		return $this->getProperty('Name');
	} // fn getName
	
	
	/**
	 * @param string $p_value
	 * @return boolean
	 */
	function setName($p_value) 
	{
	    return $this->setProperty('Name', $p_value);
	} // fn setName
	
	
	/**
	 * @return string
	 */
	function getUrlName() 
	{
		return $this->getProperty('ShortName');
	} // fn getUrlName
	
	
	/**
	 * @param string $p_name
	 */
	function setUrlName($p_name) 
	{
	    return $this->setProperty('ShortName', $p_name);
	} // fn setUrlName
	
	
	/**
	 * @return int
	 */
	function getArticleTemplateId() 
	{
		return $this->getProperty('ArticleTplId');
	} // fn getArticleTemplateId
	
	
	/**
	 * @param int $p_value
	 * @return boolean
	 */
	function setArticleTemplateId($p_value)
	{
		return $this->setProperty('ArticleTplId', $p_value);
	} // fn setArticleTemplateId
	
	
	/**
	 * @return int
	 */
	function getSectionTemplateId() 
	{
		return $this->getProperty('SectionTplId');
	} // fn getSectionTemplateId
	
	
	/**
	 * @param int $p_value
	 * @return boolean
	 */
	function setSectionTemplateId($p_value) 
	{
		return $this->setProperty('SectionTplId', $p_value);
	} // fn setSectionTemplateId
	
	
	/**
	 * Return an array of sections in the given issue.
	 * @param int $p_publicationId
	 * 		(Optional) Only return sections in this publication.
	 *
	 * @param int $p_issueId
	 *		(Optional) Only return sections in this issue.
	 *
	 * @param int $p_languageId
	 * 		(Optional) Only return sections that have this language.
	 *
	 * @param array $p_sqlOptions
	 *		(Optional) Additional options.  See DatabaseObject::ProcessOptions().
	 *
	 * @return array
	 */
	function GetSections($p_publicationId = null, $p_issueId = null,
	                     $p_languageId = null, $p_sqlOptions = null) 
	{
		$constraints = array();
		if (!is_null($p_publicationId)) {
			$constraints[] = array("IdPublication", $p_publicationId);
		}
		if (!is_null($p_issueId)) {
			$constraints[] = array("NrIssue", $p_issueId);
		}
		if (!is_null($p_languageId)) {
			$constraints[] = array("IdLanguage", $p_languageId);
		}
		
		return DatabaseObject::Search('Section', $constraints, $p_sqlOptions);
	} // fn GetSections
	
	
	/**
	 * Return an array of arrays indexed by "id" and "name".
	 * @return array
	 */
	function GetUniqueSections($p_publicationId)
	{
		global $Campsite;
		$tmpObj =& new Section();		
		$queryStr = "SELECT DISTINCT Number as id, Name as name "
					." FROM ".$tmpObj->m_dbTableName
					." WHERE IdPublication=$p_publicationId";
		return $Campsite['db']->GetAll($queryStr);
	} // fn GetSectionNames
	
	
	/**
	 * Return the total number of sections according to the given values.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_languageId
	 * @return int
	 */
	function GetTotalSections($p_publicationId = null, $p_issueId = null, $p_languageId = null) 
	{
		global $Campsite;
		$queryStr = 'SELECT COUNT(*) FROM Sections';
		$whereClause = array();
		if (!is_null($p_publicationId)) {
			$whereClause[] = "IdPublication=$p_publicationId";
		}
		if (!is_null($p_issueId)) {
			$whereClause[] = "NrIssue=$p_issueId";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "IdLanguage=$p_languageId";
		}
		if (count($whereClause) > 0) {
			$queryStr .= ' WHERE '.implode(' AND ', $whereClause);
		}
		$total = $Campsite['db']->GetOne($queryStr);
		return $total;
	} // fn GetTotalSections
	
	
	/**
	 * Return a section number that is not in use.
	 * @param int $p_publicationId
	 * @param int $p_issueId
	 * @param int $p_languageId
	 * @return int
	 */
	function GetUnusedSectionId($p_publicationId, $p_issueId, $p_languageId) 
	{
		global $Campsite;
		$queryStr = "SELECT MAX(Number) + 1 FROM Sections "
					." WHERE IdPublication=$p_publicationId "
					." AND NrIssue=$p_issueId AND IdLanguage=$p_languageId";
		$number = $Campsite['db']->GetOne($queryStr);
		return $number;
	} // fn GetUnusedSectionId
		
} // class Section
?>