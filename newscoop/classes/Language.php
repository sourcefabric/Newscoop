<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
global $ADMIN_DIR;

require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');
require_once($GLOBALS['g_campsiteDir'].'/conf/configuration.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

/**
 * @package Campsite
 */
class Language extends DatabaseObject {
	var $m_dbTableName = 'Languages';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'CodePage', 'OrigName',
	    'Code', 'Month1', 'Month2', 'Month3', 'Month4', 'Month5',
	    'Month6', 'Month7', 'Month8', 'Month9', 'Month10', 'Month11',
	    'Month12', 'WDay1', 'WDay2', 'WDay3', 'WDay4', 'WDay5', 'WDay6',
	    'WDay7', 'ShortMonth1', 'ShortMonth2', 'ShortMonth3', 'ShortMonth4',
	    'ShortMonth5', 'ShortMonth6', 'ShortMonth7', 'ShortMonth8',
	    'ShortMonth9', 'ShortMonth10', 'ShortMonth11', 'ShortMonth12',
	    'ShortWDay1', 'ShortWDay2', 'ShortWDay3', 'ShortWDay4',
	    'ShortWDay5', 'ShortWDay6', 'ShortWDay7');

	/**
	 * Constructor.
	 * @param int $p_languageId
	 */
	public function Language($p_languageId = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		if (!is_null($p_languageId)) {
    		$this->m_data['Id'] = $p_languageId;
			$this->fetch();
		}
	} // constructor


	/**
	 * Create the language.  Creates the directory on disk to store the
	 * translation files.
	 *
	 * @param array $p_values
	 * @return mixed
	 * 		Return TRUE on success and PEAR_Error on failure.
	 */
	public function create($p_values = null)
	{
		$success = parent::create($p_values);
		if ($success) {
		        $result = Localizer::CreateLanguageFiles($this->m_data['Code']);
			if (PEAR::isError($result)) {
			        $this->delete(false);
				return $result;
			}
			CampCache::singleton()->clear('user');
			if (function_exists("camp_load_translation_strings")) {
			        camp_load_translation_strings("api");
			}
			$logtext = getGS('Language "$1" ($2) added', $this->m_data['Name'], $this->m_data['OrigName']);
			Log::Message($logtext, null, 101);
		}
		return $success;
	} // fn create


	/**
	 * Update the language.
	 *
	 * @param array $p_values
	 * @param boolean $p_commit
	 * @param boolean $p_isSql
	 * @return boolean
	 */
	public function update($p_values = null, $p_commit = true, $p_isSql = false)
	{
		$success = parent::update($p_values, $p_commit, $p_isSql);
		if (!$success) {
			return false;
		}
		CampCache::singleton()->clear('user');
		if (function_exists("camp_load_translation_strings")) {
			camp_load_translation_strings("api");
		}
		$logtext = getGS('Language "$1" ($2) modified', $this->m_data['Name'], $this->m_data['OrigName']);
		Log::Message($logtext, null, 103);
		return $success;
	} // fn update


	/**
	 * Delete the language, this will also delete the language files unless
	 * the parameter specifies otherwise.
	 *
	 * @return boolean
	 */
	public function delete($p_deleteLanguageFiles = true)
	{
		if (is_link($GLOBALS['g_campsiteDir'] . '/' . $this->getCode() . '.php')) {
			unlink($GLOBALS['g_campsiteDir'] . '/' . $this->getCode() . '.php');
		}
		if ($p_deleteLanguageFiles) {
			$result = Localizer::DeleteLanguageFiles($this->getCode());
			if (PEAR::isError($result)) {
				return result;
			}
		}
		$tmpData = $this->m_data;
		$success = parent::delete();
		if ($success) {
		        CampCache::singleton()->clear('user');
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Language "$1" ($2) deleted', $tmpData['Name'], $tmpData['OrigName']);
			Log::Message($logtext, null, 102);
		}
		return $success;
	} // fn delete


	/**
	 * The unique ID of the language in the database.
	 * @return int
	 */
	public function getLanguageId()
	{
		return $this->m_data['Id'];
	} // fn getLanguageId


	/**
	 * Return the english name of this language.
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * Return the name of the language as written in the language itself.
	 * @return string
	 */
	public function getNativeName()
	{
		return $this->m_data['OrigName'];
	} // fn get


	/**
	 * Get the two-letter code for this language.
	 * @return string
	 */
	public function getCode()
	{
		return $this->m_data['Code'];
	} // fn getCode


	/**
	 * Get the page encoding for this language.
	 * @return string
	 */
	public function getCodePage()
	{
	    return $this->m_data['CodePage'];
	} // fn getCodePage


	/**
	 * Return an array of Language objects based on the given contraints.
	 *
	 * @param int $p_id
	 * @param string $p_languageCode
	 * @param string $p_name
	 * @param array $p_excludedLanguages
	 * @param array $p_order
	 * @return array
	 */
	public static function GetLanguages($p_id = null, $p_languageCode = null,
	$p_name = null, array $p_excludedLanguages = array(), array $p_order = array(),
	$p_skipCache = false)
	{
	    global $g_ado_db;

	    if (!$p_skipCache && CampCache::IsEnabled()) {
	    	$paramsArray['id'] = (is_null($p_id)) ? 'null' : $p_id;
	    	$paramsArray['language_code'] = (is_null($p_languageCode)) ? 'null' : $p_languageCode;
	    	$paramsArray['name'] = (is_null($p_name)) ? 'null' : $p_name;
	    	$paramsArray['excluded_languages'] = $p_excludedLanguages;
	    	$paramsArray['order'] = $p_order;
	    	$cacheListObj = new CampCacheList($paramsArray, __METHOD__);
	    	$languages = $cacheListObj->fetchFromCache();
	    	if ($languages !== false && is_array($languages)) {
	    		return $languages;
	    	}
	    }

	    $selectClauseObj = new SQLSelectClause();
	    $tmpLanguage = new Language();
	    $selectClauseObj->setTable($tmpLanguage->getDbTableName());

	    if (!is_null($p_id)) {
	    	$selectClauseObj->addWhere('Id = ' . (int)$p_id);
	    }
	    if (!is_null($p_languageCode)) {
	    	$selectClauseObj->addWhere("Code = '" . $g_ado_db->escape($p_languageCode) . "'");
	    }
	    if (!is_null($p_name)) {
            $selectClauseObj->addWhere("Name = '" . $g_ado_db->escape($p_name) . "'");
	    }
	    if (count($p_excludedLanguages) > 0) {
	    	$excludedLanguages = array();
	    	foreach ($p_excludedLanguages as $excludedLanguage) {
	    		$excludedLanguages[] = (int)$excludedLanguage;
	    	}
	    	$selectClauseObj->addWhere("Id NOT IN (" . implode(', ', $excludedLanguages) . ")");
	    }
	    $order = Language::ProcessLanguageListOrder($p_order);
	    foreach ($order as $orderDesc) {
	        $selectClauseObj->addOrderBy($orderDesc['field'] . ' ' . $orderDesc['dir']);
	    }
	    $selectClause = $selectClauseObj->buildQuery();
	    $languages = DbObjectArray::Create('Language', $selectClause);
	    if (!$p_skipCache && CampCache::IsEnabled()) {
	        $cacheListObj->storeInCache($languages);
	    }

	    return $languages;
	} // fn GetLanguages


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


	/**
	 * Returns language id for the provided language code
	 *
	 */
	public static function GetLanguageIdByCode($p_languageCode)
	{
	    global $g_ado_db;

        $queryStr = "SELECT Id FROM Languages WHERE Code = ? LIMIT 1";
        $queryParams = array($p_languageCode);

        $result = $g_ado_db->GetAll($queryStr, $queryParams);

        if ((!$result) || (1 > count($result))) {return null;}

        return $result[0]['Id'];
    } // fn GetLanguageIdByCode


} // class Language