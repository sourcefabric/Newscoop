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
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');

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
	                           'comments_public_moderated',
	                           'comments_captcha_enabled',
	                           'comments_spam_blocking_enabled',
							   'url_error_tpl_id',
                               'seo',
	                           'comments_public_enabled',
                               'comments_moderator_to',
	                           'comments_moderator_from'
	                           );

	/**
	 * A publication represents a magazine or newspaper.
	 *
	 * This class is mainly responsible for specifying
	 * publication-wide configuration parameters.
	 *
	 * @param int $p_publicationId
	 */
	public function Publication($p_publicationId = null)
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
	public function create($p_values = null)
	{
		$created = parent::create($p_values);
		if ($created) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Publication "$1" ($2) added', $this->m_data['Name'], $this->m_data['Id']);
			Log::Message($logtext, null, 1);
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
	public function update($p_columns = null, $p_commit = true, $p_isSql = false)
	{
		$updated = parent::update($p_columns, $p_commit, $p_isSql);
		if ($updated) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Publication "$1" ($2) changed', $this->m_data['Name'], $this->m_data['Id']);
			Log::Message($logtext, null, 3);
		}
		return $updated;
	} // fn update


	/**
	 * Delete the publication and all of its aliases.
	 *
	 * @return boolean
	 */
	public function delete()
	{
		$aliases = Alias::GetAliases(null, $this->m_data['Id']);
		if ($aliases && (count($aliases) > 0)) {
			foreach ($aliases as $alias) {
				$alias->delete();
			}
		}
		$tmpData = $this->m_data;
		$deleted = parent::delete();
		if ($deleted) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Publication "$1" ($2) deleted', $tmpData['Name'], $tmpData['Id']);
			Log::Message($logtext, null, 2);
		}
		return $deleted;
	} // fn delete


	/**
	 * Get the unique ID for this publication.
	 *
	 * @return int
	 */
	public function getPublicationId()
	{
		return $this->m_data['Id'];
	} // fn getPublicationId


	/**
	 * Get the name of this publication.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * Get the default language for this publication.
	 *
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->m_data['IdDefaultLanguage'];
	} // fn getLanguageId


	/**
	 * @return string
	 */
	public function getTimeUnit()
	{
		return $this->m_data['TimeUnit'];
	} // fn getTimeUnit


	/**
	 * Returns the publication subscription time unit name.
	 *
	 * @param int $p_languageId
	 * @return string
	 */
	public function getTimeUnitName($p_languageId = null)
	{
	    $languageId = is_null($p_languageId) ? $this->m_data['IdDefaultLanguage'] : $p_languageId;
	    $timeUnit = new TimeUnit($this->m_data['TimeUnit'], $languageId);
	    if (!$timeUnit->exists()) {
	        $timeUnit = new TimeUnit($this->m_data['TimeUnit'], 1);
	    }
	    return $timeUnit->getName();
	}




	/**
	 * @return int
	 */
	public function getDefaultAliasId()
	{
		return $this->m_data['IdDefaultAlias'];
	} // fn getDefaultAliasId


	/**
	 * Get the default language for this publication.
	 *
	 * @return int
	 */
	public function getDefaultLanguageId()
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
	public function getUrlTypeId()
	{
		return $this->m_data['IdURLType'];
	} // fn getUrlTypeId


	/**
	 * @return float
	 */
	public function getUnitCost()
	{
		return $this->m_data['UnitCost'];
	} // fn getUnitCost


	/**
	 * @return float
	 */
	public function getUnitCostAllLang()
	{
		return $this->m_data['UnitCostAllLang'];
	} // fn getUnitCost


	/**
	 * @return string
	 */
	public function getCurrency()
	{
		return $this->m_data['Currency'];
	} // fn getCurrency


	/**
	 * @return int
	 */
	public function getPaidTime()
	{
		return $this->m_data['PaidTime'];
	} // fn getPaidTime


	/**
	 * @return int
	 */
	public function getTrialTime()
	{
		return $this->m_data['TrialTime'];
	} // fn getTrialTime


	/**
	 * Return the forum associated with this publication.
	 *
	 * @return int
	 */
	public function getForumId()
	{
	    return $this->m_data['fk_forum_id'];
	} // fn getForumId

	/**
     * Return the SEO params.
     *
     * @return int
     */
    public function getSeo()
    {
        return @unserialize($this->m_data['seo']);
    } // fn getSeo


	public function setForumId($p_value)
	{
	    return $this->setProperty('fk_forum_id', $p_value);
	} // fn setForumId


	/**
	 * Alias for getCommentsEnabled
	 *
	 * @return boolean
	 */
	public function commentsEnabled()
	{
	    return $this->getCommentsEnabled();
	} // commentsEnabled

    /**
     * Return TRUE if comments are enabled for this publication.
     *
     * @return boolean
     */
    public function getCommentsEnabled()
    {
        return $this->m_data['comments_enabled'];
    } // commentsEnabled

	/**
	 * Set whether comments are enabled for this publication.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	public function setCommentsEnabled($p_value)
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
	public function commentsArticleDefaultEnabled()
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
	public function setCommentsArticleDefaultEnabled($p_value)
	{
	    $p_value = $p_value ? '1' : '0';
	    return $this->setProperty('comments_article_default_enabled', $p_value);
	} // fn setCommentsArticleDefaultEnabled


	/**
	 * Return TRUE if comments made by subscribers are moderated.
	 *
	 * @return boolean
	 */
	public function commentsSubscribersModerated()
	{
	    return $this->m_data['comments_subscribers_moderated'];
	} // fn commentsSubscribersModerated


	/**
	 * Set whether subscriber comments are moderated.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
	public function setCommentsSubscribersModerated($p_value)
	{
	    $p_value = $p_value ? '1' : '0';
	    return $this->setProperty('comments_subscribers_moderated', $p_value);
	} // fn setCommentsSubscribersModerated


	/**
	 * Get whether comments made by the public are moderated.
	 *
	 * @return boolean
	 */
	public function commentsPublicModerated()
	{
	    return $this->m_data['comments_public_moderated'];
	} // fn commentsPublicModerated


	/**
	 * Set whether public comments are moderated.
	 *
	 * @param boolean $p_value
	 * @return boolean
	 */
    public function setCommentsPublicModerated($p_value)
    {
	    $p_value = $p_value ? '1' : '0';
        return $this->setProperty('comments_public_moderated', $p_value);
    } // fn setCommentsPublicModerated


    /**
     * Return TRUE if a CAPTCHA is used to block comment spam when a
     * user is posting.
     *
     * @return boolean
     */
    public function isCaptchaEnabled()
    {
    	return $this->m_data['comments_captcha_enabled'];
    } // fn isCaptchaEnabled


    /**
     * Set whether a CAPTCHA should be used to block comment spam when
     * a user is posting.
     *
     * @param boolean $p_value
     * @return boolean
     */
    public function setCaptchaEnabled($p_value)
    {
	    $p_value = $p_value ? '1' : '0';
        return $this->setProperty('comments_captcha_enabled', $p_value);
    } // fn setCaptchaEnabled


    /**
     * Return TRUE if comments are checked if they are SPAM before they
     * are accepted.
     *
     * @return boolean
     */
    public function isSpamBlockingEnabled()
    {
    	return $this->m_data['comments_spam_blocking_enabled'];
    } // fn isSpamBlockingEnabled


    /**
     * Set whether to check comments for SPAM before posting them.
     *
     * @param boolean $p_value
     * @return boolean
     */
    public function setSpamBlockingEnabled($p_value)
    {
	    $p_value = $p_value ? '1' : '0';
        return $this->setProperty('comments_spam_blocking_enabled', $p_value);
    } // fn setSpamBlockingEnabled

    /**
     * Alias for getPublicComments
     *
     * @return bool
     */
    public function publicComments() {
        return $this->getPublicComments();
    }

    /**
     * Return true if comments can be posted by unknown readers.
     *
     * @return bool
     */
    public function getPublicComments() {
        return $this->m_data['comments_public_enabled'];
    }


    /**
     * Set a flag that controls whether an unknown user may post comments.
     *
     * @param boolean $isOn
     * @return boolean
     */
    public function setPublicComments($p_value) {
        $p_value = $p_value ? '1' : '0';
        return $this->setProperty('comments_public_enabled', $p_value);
    }

    /**
     * Get moderator email address for this publication.
     *
     * @return string
     */
    public function getCommentsModeratorTo()
    {
        return $this->m_data['comments_moderator_to'];
    }

    /**
     * Set moderator email adddress to which info email is send.
     *
     * @param string $p_value
     * @return boolean
     */
    public function setCommentsModeratorTo($p_value)
    {
        return $this->setProperty('comments_moderator_to', $p_value);
    }

    /**
     * Get from which address moderator receives the info email.
     *
     * @return string
     */
    public function getCommentsModeratorFrom()
    {
        return $this->m_data['comments_moderator_from'];
    }

    /**
     * Set from which address moderator receives the info email.
     *
     * @param string $p_value
     * @return boolean
     */
    public function setCommentsModeratorFrom($p_value)
    {
        return $this->setProperty('comments_moderator_from', $p_value);
    }

	/**
	 * Return all languages used in the publication as an array of
	 * Language objects.
	 *
     * @param integer $p_excludeLanguageId
     *      If greater than 0, exclude the language with the given
     *      identifier from the list.
     * @param array $p_order
     *      The array of order directives in the format:
     *      array('field'=>field_name, 'dir'=>order_direction)
     *      field_name can take one of the following values:
     *        bynumber, byname, byenglish_name, bycode
     *      order_direction can take one of the following values:
     *        asc, desc
	 * @return array
	 */
	public function getLanguages($p_excludeLanguageId = null,
	array $p_order = array(), $p_published = false)
	{
		$queryStr = 'SELECT Languages.* FROM Issues LEFT JOIN Languages '
		          . 'ON Issues.IdLanguage = Languages.Id WHERE '
		          . 'Issues.IdPublication = ' . $this->getPublicationId();
        if ($p_excludeLanguageId > 0) {
        	$queryStr .= ' AND Languages.Id != ' . $p_excludeLanguageId;
        }
        if ($p_published) {
            $queryStr .= " AND Issues.Published = 'Y'";
        }
        $queryStr .= ' GROUP BY Languages.Id';
        $order = Publication::ProcessLanguageListOrder($p_order);
        foreach ($order as $orderDesc) {
            $sqlOrder[] = $orderDesc['field'] . ' ' . $orderDesc['dir'];
        }
        if (count($sqlOrder) > 0) {
            $queryStr .= ' ORDER BY ' . implode(', ', $sqlOrder);
        }
		$languages = DbObjectArray::Create('Language', $queryStr);
		return $languages;
	}


	/**
	 * Return the total number of publications.
	 *
	 * @return int
	 */
	public static function GetNumPublications()
	{
	    global $g_ado_db;
	    $queryStr = "SELECT COUNT(*) FROM Publications";
	    return $g_ado_db->GetOne($queryStr);
	} // fn GetNumPublications


	/**
	 * Return all publications as an array of Publication objects.
	 *
	 * @param string $p_name
	 * @param int $p_aliasId
	 * @param array $p_sqlOptions
	 *
	 * @return array
	 */
	public static function GetPublications($p_name = null, $p_aliasId = null,
	                                       $p_sqlOptions = null)
	{
	    if (is_null($p_sqlOptions)) {
	        $p_sqlOptions = array();
	    }
	    if (!isset($p_sqlOptions["ORDER BY"])) {
	        $p_sqlOptions["ORDER BY"] = array("Name" => "ASC");
	    }
	    $tmpPub = new Publication();
	    $columns = $tmpPub->getColumnNames(true);
		$queryStr = 'SELECT '.implode(',', $columns)
		            .', Aliases.Name as Alias'
		            .', URLTypes.Name as URLType'
		            .', Languages.OrigName as NativeName'
		            .' FROM Publications, Languages, Aliases, URLTypes'
		            .' WHERE Publications.IdDefaultAlias = Aliases.Id '
		            .' AND Publications.IdURLType = URLTypes.Id '
		            .' AND Publications.IdDefaultLanguage = Languages.Id ';
		if (is_string($p_name)) {
			$queryStr .= " AND Publications.Name='".mysql_real_escape_string($p_name)."'";
		}
		if (is_numeric($p_aliasId)) {
			$queryStr .= " AND Publications.IdDefaultAlias=$p_aliasId";
		}
        $queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		$publications = DbObjectArray::Create('Publication', $queryStr);
		return $publications;
	} // fn getPublications


    /**
     * Processes an order directive for the issue translations list.
     *
     * @param array $p_order
     *      The array of order directives in the format:
     *      array('field'=>field_name, 'dir'=>order_direction)
     *      field_name can take one of the following values:
     *        bynumber, byname, byenglish_name, bycode
     *      order_direction can take one of the following values:
     *        asc, desc
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessLanguageListOrder(array $p_order)
    {
        $order = array();
        foreach ($p_order as $orderDesc) {
            $field = $orderDesc['field'];
            $direction = $orderDesc['dir'];
            $dbField = null;
            switch (strtolower($field)) {
                case 'bynumber':
                    $dbField = 'Languages.Id';
                    break;
                case 'byname':
                    $dbField = 'Languages.OrigName';
                    break;
                case 'byenglish_name':
                    $dbField = 'Languages.Name';
                    break;
                case 'bycode':
                    $dbField = 'Languages.Code';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[] = array('field'=>$dbField, 'dir'=>$direction);
        }
        return $order;
    }

} // class Publication
?>