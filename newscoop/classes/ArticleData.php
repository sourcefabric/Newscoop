<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/DatabaseObject.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTypeField.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');

/**
 * @package Campsite
 */
class ArticleData extends DatabaseObject {
    var $m_columnNames = array('NrArticle', 'IdLanguage');
    var $m_keyColumnNames = array('NrArticle', 'IdLanguage');
    var $m_dbTableName;
    var $m_articleTypeName;
    private $m_articleTypeObject = null;

    /**
     * An article type is a dynamic table that is created for an article
     * to allow different publications to display their content in different
     * ways.
     *
     * @param string $p_articleType
     * @param int $p_articleNumber
     * @param int $p_languageId
     */
    public function ArticleData($p_articleType, $p_articleNumber, $p_languageId)
    {
        $this->m_articleTypeName = $p_articleType;
        $this->m_dbTableName = 'X'.$p_articleType;
        if (empty($this->m_articleTypeName)) {
            return;
        }
        // Get user-defined values.
        $dbColumns = $this->getUserDefinedColumns(true);
        foreach ($dbColumns as $columnMetaData) {
            $this->m_columnNames[] = $columnMetaData->getName();
        }
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['NrArticle'] = $p_articleNumber;
        $this->m_data['IdLanguage'] = $p_languageId;
        if ($this->keyValuesExist()) {
            $this->fetch();
        }
    } // constructor


    public function getFieldValue($p_property, $p_forceFetchFromDatabase = false)
    {
        $dbColumnName = 'F'.$p_property;
        return parent::getProperty($dbColumnName, $p_forceFetchFromDatabase);
    }


    /**
     * Gets the translation for a given language; default language is the
     * session language.  If no translation is set for that language, we
     * return the dbTableName.
     *
     * @param int p_lang
     *
     * @return string
     */
    public function getDisplayName($p_lang = 0)
    {
        if (!$p_lang) {
            $lang = camp_session_get('LoginLanguageId', 1);
        } else {
            $lang = $p_lang;
        }
        $aObj = new ArticleType($this->m_articleTypeName);
        $translations = $aObj->getTranslations();
        if (!isset($translations[$lang])) return substr($aObj->getTableName(), 1);
        return $translations[$lang];

    } // fn getDisplayName


    public function setProperty($p_dbColumnName, $p_value, $p_commit = true, $p_isSql = false)
    {
        if (!in_array($p_dbColumnName, $this->m_columnNames)) {
            return false;
        }
        $articleField = new ArticleTypeField($this->m_articleTypeName, substr($p_dbColumnName, 1));
        if ($articleField->getType() == ArticleTypeField::TYPE_BODY) {
            // Replace <span class="subhead"> ... </span> with <!** Title> ... <!** EndTitle>
            $text = preg_replace_callback("/(<\s*span[^>]*class\s*=\s*[\"']campsite_subhead[\"'][^>]*>|<\s*span|<\s*\/\s*span\s*>)/i",
            array('ArticleData', "TransformSubheads"), $p_value);

            // Replace <a href="campsite_internal_link?IdPublication=1&..." ...> ... </a>
            // with <!** Link Internal IdPublication=1&...> ... <!** EndLink>
            $text = preg_replace_callback("/(<\s*a\s*(((href\s*=\s*[\"'](\\/campsite\\/)?campsite_internal_link[?][\w&=;]*[\"'])|(\w+\s*=\s*['\"][_\w]*['\"]))+[\s]*)*[\s\w\"']*>)|(<\s*\/a\s*>)/i",
            array('ArticleData', "TransformInternalLinks"), $text);

            // Replace <img id=".." src=".." alt=".." title=".." align="..">
            // with <!** Image [image_template_id] align=".." alt=".." sub="..">
            $idAttr = "(id\s*=\s*\"[^\"]*\")";
            $srcAttr = "(src\s*=\s*\"[^\"]*\")";
            $altAttr = "(alt\s*=\s*\"[^\"]*\")";
            $subAttr = "(title\s*=\s*\"[^\"]*\")";
            $alignAttr = "(align\s*=\s*\"[^\"]*\")";
            $widthAttr = "(width\s*=\s*\"[^\"]*\")";
            $heightAttr = "(height\s*=\s*\"[^\"]*\")";
            $otherAttr = "(\w+\s*=\s*\"[^\"]*\")*";
            $pattern = "/<\s*img\s*(($idAttr|$srcAttr|$altAttr|$subAttr|$alignAttr|$widthAttr|$heightAttr|$otherAttr)\s*)*\/>/i";
            $p_value = preg_replace_callback($pattern, array($this, "transformImageTags"), $text);
        }
        if ($articleField->getType() == ArticleTypeField::TYPE_SWITCH) {
            return parent::setProperty($p_dbColumnName, (int)($p_value == 'on'), $p_commit);
        }
        return parent::setProperty($p_dbColumnName, $p_value, $p_commit, $p_isSql);
    }


    /**
     * Copy the row in the database.
     * @param int $p_destArticleNumber
     * @return void
     */
    public function copy($p_destArticleNumber)
    {
        global $g_ado_db;
        $tmpData = $this->m_data;
        unset($tmpData['NrArticle']);
        foreach ($tmpData as $key => $data) {
            $tmpData[$key] = "'".$data."'";
        }

        $queryStr = 'INSERT IGNORE INTO '.$this->m_dbTableName
            .'(NrArticle,'.implode(',', array_keys($this->m_columnNames)).')'
            .' VALUES ('.$p_destArticleNumber.','.implode(',', $tmpData).')';
        $g_ado_db->Execute($queryStr);
    } // fn copy


    /**
    * Return an array of ArticleTypeField objects.
    *
    * @param p_showAll boolean
    *
    * @return array
    */
    public function getUserDefinedColumns($p_showAll = false, $p_skipCache = false)
    {
        if (empty($this->m_articleTypeName)) {
            return array();
        }

        if (is_null($this->m_articleTypeObject)) {
            $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
            $cacheKey = $cacheService->getCacheKey('article_type_'.$this->m_articleTypeName, 'article_type');
            if ($cacheService->contains($cacheKey)) {
                $this->m_articleTypeObject = $cacheService->fetch($cacheKey);
            } else {
                $this->m_articleTypeObject = new ArticleType($this->m_articleTypeName);
                $cacheService->save($cacheKey, $this->m_articleTypeObject);
            }
        }

        return $this->m_articleTypeObject->getUserDefinedColumns(null, $p_showAll, $p_skipCache);
    }

    /**
     * Copy the row in the database.
     * @param int $p_destArticleNumber
     * @param int $p_destLanguageId
     * @return void
     */
    public function copyToExistingRecord($p_destArticleNumber, $p_destLanguageId = null)
    {
        global $g_ado_db;
        $tmpData = $this->m_data;
        unset($tmpData['NrArticle']);
        unset($tmpData['IdLanguage']);
        $setQuery = array();
        foreach ($tmpData as $key => $data) {
            $setQuery[] = $g_ado_db->escapeKeyVal($key, $data);
        }
        $queryStr = 'UPDATE '.$this->m_dbTableName.' SET '.implode(',', $setQuery)
                ." WHERE NrArticle=$p_destArticleNumber ";
        if (!is_null($p_destLanguageId)) {
            $queryStr .= " AND IdLanguage=".$p_destLanguageId;
        } else {
            $queryStr .= " AND IdLanguage=".$this->m_data['IdLanguage'];
        }
        $g_ado_db->Execute($queryStr);
    } // fn copyToExistingRecord


    public static function TransformSubheads($match) {
        static $spanCounter = -1;

        // This matches '<span class="campsite_subhead">'
        if (preg_match("/<\s*span[^>]*class\s*=\s*[\"']campsite_subhead[\"'][^>]*>/i", $match[0])) {
            $spanCounter = 1;
            return "<!** Title>";
        }
        // This matches '<span'
        elseif (($spanCounter >= 0) && preg_match("/<\s*span/i", $match[0])) {
            $spanCounter += 1;
        }
        // This matches '</span>'
        elseif (($spanCounter >= 0) && preg_match("/<\s*\/\s*span\s*>/i", $match[0])) {
            $spanCounter -= 1;
        }
        if ($spanCounter == 0) {
            $spanCounter = -1;
            return "<!** EndTitle>";
        }
        return $match[0];
    } // fn TransformSubheads


    /**
     * This function is a callback for preg_replace_callback().
     * It will replace <a href="campsite_internal_link?...">...</a>
     * with <!** Link Internal ...> ... <!** EndLink>
     * @param array p_match
     * @return string
     */
    public static function TransformInternalLinks($p_match) {
        static $internalLinkCounter = 0;
        static $internalLinkStartTag = 0;

        // This matches anchor links
        $anchorStartRegex = "/<\s*a\s*(name\s*=\s*[\"']\w+[\"'])+[\s]*>/i";
        if (preg_match($anchorStartRegex, $p_match[0])) {
            // Leave the HTML tag as is
            return $p_match[0];
        }

        // This matches '<a href="campsite_internal_link?IdPublication=1&..." ...>'
        $internalLinkStartRegex = "/<\s*a\s*(((href\s*=\s*[\"'](\\/campsite\\/)?campsite_internal_link[?][\w&=;]*[\"'])|(\w+\s*=\s*['\"][_\w]*['\"]))[\s]*)*[\s\w\"']*>/i";

        // This matches '</a>'
        $internalLinkEndRegex = "/<\s*\/a\s*>/i";

        if (preg_match($internalLinkEndRegex, $p_match[0])) {
            // Check if we are closing an internal link
            if ($internalLinkCounter > 0) {
                $internalLinkCounter = 0;
                // Make sure the starting link was not blank (a blank
                // indicates it was a link to no where)
                if ($internalLinkStartTag != "") {
                    // Replace the HTML tag with a template tag
                    $retval = "<!** EndLink>";
                    $internalLinkStartTag = "";
                    return $retval;
                } else {
                    // The starting link was blank, so we return blank for the
                    // ending link.
                    return "";
                }
            } else {
                // Leave the HTML tag as is (for external links).
                return '</a>';
            }
        } elseif (preg_match($internalLinkStartRegex, $p_match[0])) {
            // Get the URL
            preg_match("/href\s*=\s*[\"'](\\/campsite\\/)?(campsite_internal_link[?][\w&=;]*)[\"']/i", $p_match[0], $url);
            $url = isset($url[2]) ? $url[2] : '';
            $parsedUrl = parse_url($url);
            $parsedUrl = str_replace("&amp;", "&", $parsedUrl);

            $retval = "";
            // It's possible that there isnt a query string - in which case
            // its a link to no where, so we remove it ($retval is empty
            // string).
            if (isset($parsedUrl["query"])) {
                // Get the target, if there is one
                preg_match("/target\s*=\s*[\"']([_\w]*)[\"']/i", $p_match[0], $target);
                $target = isset($target[1]) ? $target[1] : null;

                // Replace the HTML tag with a template tag
                $retval = "<!** Link Internal ".$parsedUrl["query"];
                if (!is_null($target)) {
                    $retval .= " TARGET ".$target;
                }
                $retval .= ">";
            }

            // Mark that we are now inside an internal link.
            $internalLinkCounter = 1;
            // Remember the starting link tag
            $internalLinkStartTag = $retval;

            return $retval;
        }
    } // fn TransformInternalLinks


    /**
     * This function is a callback for preg_replace_callback().
     * It will replace <img src="http://[hostname]/[image_dir]/cms-image-000000001.jpg" align="center" alt="alternate text" sub="caption text" id="5">
     * with <!** Image [image_template_id] align=CENTER alt="alternate text" sub="caption text">
     * @param array p_match
     * @return string
     */
    public function transformImageTags($p_match) {
        array_shift($p_match);
        $attrs = array();
        foreach ($p_match as $attr) {
            $attr = explode('=', $attr);
            if (isset($attr[0]) && !empty($attr[0])) {
                $attrName = trim(strtolower($attr[0]));
                $attrValue = isset($attr[1]) ? $attr[1] : '';
                // Strip out the quotes
                $attrValue = str_replace('"', '', $attrValue);
                $attrs[$attrName] = $attrValue;
            }
        }

        if (!isset($attrs['id'])) {
            return '';
        } else {
            if (strpos($attrs['id'], '_')) {
                list($templateId, $imageRatio) = explode('_', $attrs['id']);
            } else {
                $templateId = $attrs['id'];
            }
            $articleImage = new ArticleImage($this->m_data['NrArticle'], null, $templateId);
            if (!$articleImage->exists()) {
                return '';
            }
        }
        $alignTag = '';
        if (isset($attrs['align'])) {
            $alignTag = 'align="'.$attrs['align'].'"';
        }
        $altTag = '';
        if (isset($attrs['alt']) && strlen($attrs['alt']) > 0) {
            $altTag = 'alt="'.$attrs['alt'].'"';
        }
        $captionTag = '';
        if (isset($attrs['title']) && strlen($attrs['title']) > 0) {
            $captionTag = 'sub="'.$attrs['title'].'"';
        }
        if (isset($attrs['width']) && strlen($attrs['width']) > 0) {
            $widthTag = 'width="'.$attrs['width'].'"';
        }
        if (isset($attrs['height']) && strlen($attrs['height']) > 0) {
            $heightTag = 'height="'.$attrs['height'].'"';
        }
        $ratioTag = '';
        if (isset($imageRatio) && ($imageRatio > 0 && $imageRatio < 100)) {
            $ratioTag = 'ratio="'.$imageRatio.'"';
        }
        $imageTag = "<!** Image $templateId $alignTag $altTag $captionTag $widthTag $heightTag $ratioTag>";
        return $imageTag;
    } // fn TransformImageTags

} // class ArticleData

?>
