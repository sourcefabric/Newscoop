<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
global $ADMIN_DIR;

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
	    'Code', 'RFC3066bis', 'Month1', 'Month2', 'Month3', 'Month4', 'Month5',
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
	 * @return boolean
	 * 		Return TRUE on success and PEAR_Error on failure.
	 */
	public function create($p_values = null)
	{
		$success = parent::create($p_values);
		if ($success) {
			CampCache::singleton()->clear('user');
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

		$tmpData = $this->m_data;
		$success = parent::delete();
		if ($success) {
            CampCache::singleton()->clear('user');
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
     * Get language and regional string for this language, based on
     * RFC3066bis standard.
     * @return string
     */
    public function getRFC3066bis()
    {
        return $this->m_data['RFC3066bis'];
    } // fn getRFC3066bis


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
	    	$selectClauseObj->addWhere($g_ado_db->escapeKeyVal('Id', (int)$p_id));
	    }
	    if (!is_null($p_languageCode)) {
	    	$selectClauseObj->addWhere($g_ado_db->escapeKeyVal('Code', $p_languageCode));
	    }
	    if (!is_null($p_name)) {
            $selectClauseObj->addWhere($g_ado_db->escapeKeyVal('Name', $p_name));
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

    /**
     * Returns language for the provided language code
     */
    public static function GetLanguageByCode($languageCode)
    {
        global $g_ado_db;

        $queryStr = "SELECT * FROM Languages WHERE Code = ? LIMIT 1";
        $queryParams = array($languageCode);

        $result = $g_ado_db->GetAll($queryStr, $queryParams);

        if ((!$result) || (1 > count($result))) {return null;}

        return new Language($result[0]['Id']);
    }

    public static function Get6391List()
    {
        return array(
            'ab' => "Abkhaz",
            'aa' => "Afar",
            'af' => "Afrikaans",
            'ak' => "Akan",
            'sq' => "Albanian",
            'am' => "Amharic",
            'ar' => "Arabic",
            'an' => "Aragonese",
            'hy' => "Armenian",
            'as' => "Assamese",
            'av' => "Avaric",
            'ae' => "Avestan",
            'ay' => "Aymara",
            'az' => "Azerbaijani",
            'bm' => "Bambara",
            'ba' => "Bashkir",
            'eu' => "Basque",
            'be' => "Belarusian",
            'bn' => "Bengali",
            'bh' => "Bihari",
            'bi' => "Bislama",
            'bs' => "Bosnian",
            'br' => "Breton",
            'bg' => "Bulgarian",
            'my' => "Burmese",
            'ca' => "Catalan",
            'ch' => "Chamorro",
            'ce' => "Chechen",
            'ny' => "Chichewa",
            'zh' => "Chinese",
            'cv' => "Chuvash",
            'kw' => "Cornish",
            'co' => "Corsican",
            'cr' => "Cree",
            'hr' => "Croatian",
            'cs' => "Czech",
            'da' => "Danish",
            'dv' => "Divehi",
            'nl' => "Dutch",
            'dz' => "Dzongkha",
            'en' => "English",
            'eo' => "Esperanto",
            'et' => "Estonian",
            'ee' => "Ewe",
            'fo' => "Faroese",
            'fj' => "Fijian",
            'fi' => "Finnish",
            'fr' => "French",
            'ff' => "Fula",
            'gl' => "Galician",
            'ka' => "Georgian",
            'de' => "German",
            'el' => "Greek",
            'gn' => "Guaraní",
            'gu' => "Gujarati",
            'ht' => "Haitian",
            'ha' => "Hausa",
            'he' => "Hebrew",
            'hz' => "Herero",
            'hi' => "Hindi",
            'ho' => "Hiri Motu",
            'hu' => "Hungarian",
            'ia' => "Interlingua",
            'id' => "Indonesian",
            'ie' => "Interlingue",
            'ga' => "Irish",
            'ig' => "Igbo",
            'ik' => "Inupiaq",
            'io' => "Ido",
            'is' => "Icelandic",
            'it' => "Italian",
            'iu' => "Inuktitut",
            'ja' => "Japanese",
            'jv' => "Javanese",
            'kl' => "Kalaallisut",
            'kn' => "Kannada",
            'kr' => "Kanuri",
            'ks' => "Kashmiri",
            'kk' => "Kazakh",
            'km' => "Khmer",
            'ki' => "Kikuyu",
            'rw' => "Kinyarwanda",
            'ky' => "Kyrgyz",
            'kv' => "Komi",
            'kg' => "Kongo",
            'ko' => "Korean",
            'ku' => "Kurdish",
            'kj' => "Kwanyama",
            'la' => "Latin",
            'lb' => "Luxembourgish",
            'lg' => "Ganda",
            'li' => "Limburgish",
            'ln' => "Lingala",
            'lo' => "Lao",
            'lt' => "Lithuanian",
            'lu' => "Luba-Katanga",
            'lv' => "Latvian",
            'gv' => "Manx",
            'mk' => "Macedonian",
            'mg' => "Malagasy",
            'ms' => "Malay",
            'ml' => "Malayalam",
            'mt' => "Maltese",
            'mi' => "Māori",
            'mr' => "Marathi (Marāṭhī)",
            'mh' => "Marshallese",
            'mn' => "Mongolian",
            'na' => "Nauru",
            'nv' => "Navajo",
            'nb' => "Norwegian Bokmål",
            'nd' => "North Ndebele",
            'ne' => "Nepali",
            'ng' => "Ndonga",
            'nn' => "Norwegian Nynorsk",
            'no' => "Norwegian",
            'ii' => "Nuosu",
            'nr' => "South Ndebele",
            'oc' => "Occitan",
            'oj' => "Ojibwe",
            'cu' => "Old Church Slavonic",
            'om' => "Oromo",
            'or' => "Oriya",
            'os' => "Ossetian",
            'pa' => "Panjabi",
            'pi' => "Pāli",
            'fa' => "Persian",
            'pl' => "Polish",
            'ps' => "Pashto",
            'pt' => "Portuguese",
            'qu' => "Quechua",
            'rm' => "Romansh",
            'rn' => "Kirundi",
            'ro' => "Romanian",
            'ru' => "Russian",
            'sa' => "Sanskrit (Saṁskṛta)",
            'sc' => "Sardinian",
            'sd' => "Sindhi",
            'se' => "Northern Sami",
            'sm' => "Samoan",
            'sg' => "Sango",
            'sr' => "Serbian",
            'gd' => "Scottish Gaelic",
            'sn' => "Shona",
            'si' => "Sinhala",
            'sk' => "Slovak",
            'sl' => "Slovene",
            'so' => "Somali",
            'st' => "Southern Sotho",
            'es' => "Spanish",
            'su' => "Sundanese",
            'sw' => "Swahili",
            'ss' => "Swati",
            'sv' => "Swedish",
            'ta' => "Tamil",
            'te' => "Telugu",
            'tg' => "Tajik",
            'th' => "Thai",
            'ti' => "Tigrinya",
            'bo' => "Tibetan Standard",
            'tk' => "Turkmen",
            'tl' => "Tagalog",
            'tn' => "Tswana",
            'to' => "Tonga",
            'tr' => "Turkish",
            'ts' => "Tsonga",
            'tt' => "Tatar",
            'tw' => "Twi",
            'ty' => "Tahitian",
            'ug' => "Uighur",
            'uk' => "Ukrainian",
            'ur' => "Urdu",
            'uz' => "Uzbek",
            've' => "Venda",
            'vi' => "Vietnamese",
            'vo' => "Volapük",
            'wa' => "Walloon",
            'cy' => "Welsh",
            'wo' => "Wolof",
            'fy' => "Western Frisian",
            'xh' => "Xhosa",
            'yi' => "Yiddish",
            'yo' => "Yoruba",
            'za' => "Zhuang",
            'zu' => "Zulu",
        );
    } // fn Get6391List

} // class Language