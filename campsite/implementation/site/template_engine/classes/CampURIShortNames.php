<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

/**
 * Includes
 *
 * We indirectly reference the DOCUMENT_ROOT so we can enable
 * scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
 * is not defined in these cases.
 */
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/classes/Publication.php');
require_once($g_documentRoot.'/classes/Issue.php');
require_once($g_documentRoot.'/classes/Section.php');
require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/Alias.php');
require_once($g_documentRoot.'/template_engine/classes/CampURI.php');

define('UP_LANGUAGE_ID', 'lng_id');
define('UP_PUBLICATION_ID', 'pub_id');
define('UP_ISSUE_NR', 'iss_nr');
define('UP_SECTION_NR', 'sct_nr');
define('UP_ARTICLE_NR', 'art_nr');

/**
 * Class CampURIShortNames
 */
class CampURIShortNames extends CampURI {
    /**
     * Holds the CampURIShortNames object
     *
     * @var object
     */
    private static $m_instance = null;


	/**
	 * Class constructor
     *
     * @param string $p_uri
     *      The full URI string 
	 */
	protected function __construct($p_uri = null)
	{
        parent::__construct($p_uri);
	} // fn __construct


    /**
     * Builds an instance object of this class only if there is no one.
     *
     * @param string $p_uri
     *      The full URI string, default value 'SELF' indicates it will be
     *      fetched from the server itself.
     *
     * @return object $m_instance
     *      A CampURITemplatePath object
     */
    public static function singleton($p_uri = null)
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampURIShortNames($p_uri);
        }

        return self::$m_instance;
    } // fn singleton


	/**
	 * @return string
	 */
	public function getURI()
	{
		if (empty($this->m_query)) {
			return $this->m_path;
		}

		return $this->render(array('path', 'query'));
	} // fn getURI


    /**
     * Parses the URI in shortnames fashion and sets the corresponding
     * context values.
     *
     * TODO: Error handling and context setting
     */
    public function parse()
    {
        // gets the publication object based on site name (URI host)
        $aliasArray = Alias::GetAliases(null, null, $this->m_host);
        if (is_array($aliasArray) && sizeof($aliasArray) == 1) {
            $aliasObj = $aliasArray[0];
            $cPubId = $aliasObj->getPublicationId();
            $pubObj = new Publication($cPubId);
            if (!is_object($pubObj) || !$pubObj->exists()) {
                $cPubId = 0;
                $pubObj = null;
            }
        }
        
        if (empty($cPubId)) {
            // return error/throw exception "not valid site alias"
        }
    
        $trimmedPath = trim($this->m_path, '/');
        list($cLangCode, $cIssueSName,
             $cSectionSName, $cArticleSName) = explode('/', $trimmedPath);
        
        // gets the language identifier
        if (!empty($cLangCode)) {
            $langArray = Language::GetLanguages(null, $cLangCode);
            if (is_array($langArray) && sizeof($langArray) == 1) {
                $langObj = $langArray[0];
                $langId = $langObj->getLanguageId();
            }
        } else {
            $langId = $pubObj->getLanguageId();
        }
        
        if (empty($langId)) {
            // return error/throw exception "not valid language"
        }
        
        // gets the issue number
        if (!empty($cIssueSName)) {
            $issueArray = Issue::GetIssues($cPubId, $langId, $cIssueSName);
            if (is_array($issueArray) && sizeof($issueArray) == 1) {
                $issueObj = $issueArray[0];
                $issueNr = $issueObj->getIssueNumber();
            }
            if (empty($issueNr)) {
                // return error/throw exception "not valid issue"
            }
        } else {
            $query = "SELECT MAX(Number) FROM Issues"
                   . " WHERE IdPublication = ".$cPubId." AND IdLanguage = ".$cLangCode
                   . " AND ShortName = '".$cIssueSName."'";
            $data = $g_ado_db->GetRow($query);
            $issueNr = $data['Number'];
        }
        
        if (empty($issueNr)) {
            // return error/throw exception "not issues at all"
        }
        
        // gets the section number
        if (!empty($cSectionSName)) {
            $sectionArray = Section::GetSections($cPubId, $issueNr, $langId, $cSectionSName);
            if (is_array($sectionArray) && sizeof($sectionArray) == 1) {
                $sectionObj = $sectionArray[0];
                $sectionNr = $sectionObj->getSectionNumber();
            }
        }
        
        if (empty($sectionNr)) {
            // return error/throw exception "not valid section"
        }
        
        // gets the article number
        if (!empty($cArticleSName)) {
            $articleObj = new Article($langId, $cArticleSName);
            $articleNr = $articleObj->getArticleNumber();
        }
        
        if (empty($articleNr)) {
            // return error/throw exception "not valid article"
        }
        
    } // fn parse


	/**
     * Translates a regular URL into URL shortnames fashion.
     *
	 * @return string $uriPath
	 *      The shortnames version of the URI
	 */
	public static function BuildURI($p_uri = null)
	{
		// gets the parameters identifiers from the URI query variables
		$uriObj = new CampURI($p_uri);
		$cLangId = $uriObj->getQueryVar(UP_LANGUAGE_ID);
		$cPubId = $uriObj->getQueryVar(UP_PUBLICATION_ID);
		$cIssueNr = $uriObj->getQueryVar(UP_ISSUE_NR);
		$cSectionNr = $uriObj->getQueryVar(UP_SECTION_NR);
		$cArticleNr = $uriObj->getQueryVar(UP_ARTICLE_NR);
		
		// gets the language code
		$langObj = new Language($cLangId);
		if (is_object($langObj) && $langObj->exists()) {
			$langCode = $langObj->getCode();
		}
		
		// gets the issue short name
		$issueObj = new Issue($cPubId, $cLangId, $cIssueNr);
		if (is_object($issueObj) && $issueObj->exists()) {
			$issueSName = $issueObj->getUrlName();
		}
		
		// gets the section short name
		$sectionObj = new Section($cPubId, $cIssueNr, $cLangId, $cSectionNr);
		if (is_object($sectionObj) && $sectionObj->exists()) {
			$sectionSName = $sectionObj->getUrlName();
		}
		
		// gets the article short name
		$articleObj = new Article($cLangId, $cArticleNr);
		if (is_object($articleObj) && $articleObj->exists()) {
			$articleSName = $articleObj->getUrlName();
		}
        
        $uriPath = '/'.$langCode.'/'.$issueSName.'/'.$sectionSName.'/'.$articleSName.'/';
        
        return $uriPath;
	} // fn BuildURI

} // class CampURIShortNames

?>