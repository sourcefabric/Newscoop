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
require_once($g_documentRoot.'/classes/ParserCom.php');
require_once($g_documentRoot.'/classes/Language.php');

/**
 * @package Campsite
 */
class Publication extends DatabaseObject {
	var $m_dbTableName = 'Publications';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id',
	                           'Name',
	                           'IdDefaultLanguage',
	                           'TimeUnit',
	                           'UnitCost',
	                           'UnitCostAllLang',
	                           'Currency',
	                           'TrialTime',
	                           'PaidTime',
	                           'IdDefaultAlias',
	                           'IdURLType',
	                           'fk_forum_id',
	                           'comments_enabled',
	                           'comments_article_default_enabled',
	                           'comments_subscribers_moderated',
	                           'comments_public_moderated');

	/**
	 * A publication represents a magazine or newspaper.
	 *
	 * This class is mainly responsible for specifying
	 * publication-wide configuration parameters.
	 *
	 * @param int $p_publicationId
	 */
	function Publication($p_publicationId = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_publicationId;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor


	/**
	 * Create the publication.
	 *
	 * This is a wrapper around DatabaseObject::create(),
	 * but also logs a message and notifies the Parser.
	 *
	 * @param array $p_values
	 * @return boolean
	 */
	function create($p_values = null)
	{
		$created = parent::create($p_values);
		if ($created) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Publication $1 added', $this->m_data['Name']." (".$this->m_data['Id'].")");
			Log::Message($logtext, null, 1);
			ParserCom::SendMessage('publications', 'create', array("IdPublication" => $this->m_data['Id']));
		}
		return $created;
	} // fn create


	/**
	 * Update the specified columns in the publication.
	 *
	 * A wrapper around the DatabaseObject::update(),
	 * but this function logs a message after the update is complete
	 * and notifies the Parser about the change.
	 *
	 * @param array $p_columns
	 * @param boolean $p_commit
	 * @param boolean $p_isSql
	 * @return boolean
	 */
	function update($p_columns = null, $p_commit = true, $p_isSql = false)
	{
		$updated = parent::update($p_columns, $p_commit, $p_isSql);
		if ($updated) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Publication $1 changed', $this->m_data['Name']." (".$this->m_data['Id'].")");
			Log::Message($logtext, null, 3);
			ParserCom::SendMessage('publications', 'modify', array("IdPublication" => $this->m_data['Id']));
		}
		return $updated;
	} // fn update


	/**
	 * Delete the publication and all of its aliases.
	 *
	 * @return boolean
	 */
	function delete()
	{
		$aliases = Alias::GetAliases(null, $this->m_data['Id']);
		if ($aliases && (count($aliases) > 0)) {
			foreach ($aliases as $alias) {
				$alias->delete();
			}
		}
		$deleted = parent::delete();
		if ($deleted) {
			if (function_exists("camp_load_language")) { camp_load_language("api");	}
			$logtext = getGS('Publication $1 deleted', $this->m_data['Name']." (".$this->m_data['Id'].")");
			Log::Message($logtext, null, 2);
			ParserCom::SendMessage('publications', 'delete', array("IdPublication" => $this->m_data['Id']));
		}
		return $deleted;
	} // fn delete


	/**
	 * Get the unique ID for this publication.
	 *
	 * @return int
	 */
	function getPublicationId()
	{
		return $this->m_data['Id'];
	} // fn getPublicationId


	/**
	 * Get the name of this publication.
	 *
	 * @return string
	 */
	function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * Get the default language for this publication.
	 *
	 * @return int
	 */
	function getLanguageId()
	{
		return $this->m_data['IdDefaultLanguage'];
	} // fn getLanguageId


	/**
	 * @return string
	 */
	function getTimeUnit()
	{
		return $this->m_data['TimeUnit'];
	} // fn getTimeUnit


	/**
	 * @return int
	 */
	function getDefaultAliasId()
	{
		return $this->m_data['IdDefaultAlias'];
	} // fn getDefaultAliasId


	/**
	 * Get the default language for this publication.
	 *
	 * @return int
	 */
	function getDefaultLanguageId()
	{
		return $this->m_data['IdDefaultLanguage'];
	} // fn getDefaultLanguageId


	/**
	 * Get the URL type for this publication.
	 * This returns a key to the URLTypes table, but
	 * currently there are only two URL types:
	 * "short names" and "template path".
	 *
	 * @return int
	 */
	function getUrlTypeId()
	{
		return $this->m_data['IdURLType'];
	} // fn getUrlTypeId


	/**
	 * @return float
	 */
	function getUnitCost()
	{
		return $this->m_data['UnitCost'];
	} // fn getUnitCost


	/**
	 * @return float
	 */
	function getUnitCostAllLang()
	{
		return $this->m_data['UnitCostAllLang'];
	} // fn getUnitCost


	/**
	 * @return string
	 */
	function getCurrency()
	{
		return $this->m_data['Currency'];
	} // fn getCurrency


	/**
	 * @return int
	 */
	function getPaidTime()
	{
		return $this->m_data['PaidTime'];
	} // fn getPaidTime


	/**
	 * @return int
	 */
	function getTrialTime()
	{
		return $this->m_data['TrialTime'];
	} // fn getTrialTime


	/**
	 * Return the forum associated with this publication.
	 *
	 * @return int
	 */
	function getForumId()
	{
	    return $this->m_data['fk_forum_id'];
	} // fn getForumId


	function setForumId($p_value)
	{
	    return $this->setProperty('fk_forum_id', $p_value);
	} // fn setForumId


	/**
	 * Return TRUE if comments are enabled for this publication.
	 *
	 * @return boolean
	 */
	function commentsEnabled()
	{
	    return $this->m_data['comments_enabled'];
	} // commentsEnabled


	/**
	 * Set whether comments are enabled for this publication.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setCommentsEnabled($p_value)
	{
	    $p_value = $p_value ? '1' : '0';
	    return $this->setProperty('comments_enabled', $p_value);
	} // fn setCommentsEnabled


	/**
	 * Return TRUE if comments will default to enabled in the
	 * article edit screen.
	 *
	 * @return boolean
	 */
	function commentsArticleDefaultEnabled()
	{
	    return $this->m_data['comments_article_default_enabled'];
	} // fn commentsArticleDefaultEnabled


	/**
	 * Set whether comments will default to enabled in the
	 * article edit screen.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setCommentsArticleDefaultEnabled($p_value)
	{
	    $p_value = $p_value ? '1' : '0';
	    return $this->setProperty('comments_article_default_enabled', $p_value);
	} // fn setCommentsArticleDefaultEnabled


	/**
	 * Return TRUE if comments made by subscribers are moderated.
	 *
	 * @return boolean
	 */
	function commentsSubscribersModerated()
	{
	    return $this->m_data['comments_subscribers_moderated'];
	} // fn commentsSubscribersModerated


	/**
	 * Set whether subscriber comments are moderated.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	function setCommentsSubscribersModerated($p_value)
	{
	    $p_value = $p_value ? '1' : '0';
	    return $this->setProperty('comments_subscribers_moderated', $p_value);
	} // fn setCommentsSubscribersModerated


	/**
	 * Get whether comments made by the public are moderated.
	 *
	 * @return boolean
	 */
	function commentsPublicModerated()
	{
	    return $this->m_data['comments_public_moderated'];
	} // fn commentsPublicModerated


	/**
	 * Set whether public comments are moderated.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
    function setCommentsPublicModerated($p_value)
    {
	    $p_value = $p_value ? '1' : '0';
        return $this->setProperty('comments_public_moderated', $p_value);
    } // fn setCommentsPublicModerated


	/**
	 * Return all languages used in the publication as an array of Language objects.
	 * @return array
	 */
	function getLanguages($p_sqlOptions = null)
	{
	    if (is_null($p_sqlOptions)) {
	        $p_sqlOptions = array();
	    }
	    if (!isset($p_sqlOptions["ORDER BY"])) {
	        $p_sqlOptions["ORDER BY"] = array("l.Name" => "ASC");
	    }
		$queryStr = 'SELECT l.* FROM Issues AS i LEFT JOIN Languages AS l ON i.IdLanguage = l.Id '
				. 'WHERE i.IdPublication = ' . $this->getPublicationId() . ' GROUP BY IdLanguage';
        $queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		$languages = DbObjectArray::Create('Language', $queryStr);
		return $languages;
	}


	/**
	 * Return the total number of publications.
	 *
	 * @return int
	 */
	function GetNumPublications()
	{
	    global $g_ado_db;
	    $queryStr = "SELECT COUNT(*) FROM Publications";
	    return $g_ado_db->GetOne($queryStr);
	} // fn GetNumPublications


	/**
	 * Return all publications as an array of Publication objects.
	 *
	 * @return array
	 */
	function GetPublications($p_sqlOptions = null)
	{
	    if (is_null($p_sqlOptions)) {
	        $p_sqlOptions = array();
	    }
	    if (!isset($p_sqlOptions["ORDER BY"])) {
	        $p_sqlOptions["ORDER BY"] = array("Name" => "ASC");
	    }
	    $tmpPub =& new Publication();
	    $columns = $tmpPub->getColumnNames(true);
		$queryStr = 'SELECT '.implode(',', $columns)
		            .', Aliases.Name as Alias'
		            .', URLTypes.Name as URLType'
		            .', Languages.OrigName as NativeName'
		            .' FROM Publications, Languages, Aliases, URLTypes'
		            .' WHERE Publications.IdDefaultAlias = Aliases.Id '
		            .' AND Publications.IdURLType = URLTypes.Id '
		            .' AND Publications.IdDefaultLanguage = Languages.Id ';
        $queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		$publications = DbObjectArray::Create('Publication', $queryStr);
		return $publications;
	} // fn getPublications

} // class Publication
?>