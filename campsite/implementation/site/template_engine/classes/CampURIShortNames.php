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

define('UP_LANGUAGE_ID', 'lng_id');
define('UP_PUBLICATION_ID', 'pub_id');
define('UP_ISSUE_NR', 'iss_nr');
define('UP_SECTION_NR', 'sct_nr');
define('UP_ARTICLE_NR', 'art_nr');

/**
 * Class CampURIShortNames
 */
class CampURIShortNames {
    /**
     * @var string
     */
    private $m_path = null;

    /**
     * @var string
     */
    private $m_query = null;


	/**
	 * Class constructor
     *
     * @param string $p_uri
     *      The full URI string 
	 */
	public function __construct($p_uri = null)
	{
        $uriObj = CampURI::singleton($p_uri);
        $this->m_path = $uriObj->getPath();
        $this->m_query = $uriObj->getQuery();
	} // fn __construct


	/**
	 * @return string
	 */
	public function getURI()
	{
		if (empty($this->m_query)) {
			return $this->m_path;
		}
        $uriObj = CampURI::singleton();
		return $uriObj->render(array('path', 'query'));
	} // fn getURI


    /**
     * Parses the URI in shortnames fashion and sets the corresponding
     * context values.
     *
     * TODO: Error handling and context setting
     */
    public function parser()
    {
        $trimmedPath = trim($this->m_path, '/');
        list($cLangCode, $cIssueSName,
             $cSectionSName, $cArticleSName) = explode('/', $trimmedPath);
        
        // gets the language identifier
        if (!empty($cLangCode)) {
            $langArray = Language::GetLanguages(null, $cLangCode);
            if (sizeof($langArray) == 1) {
                $langObj = $langArray[0];
                $langId = (is_object($langObj)
                                && $langObj->exists()) ? $langObj->getLanguageId() : '';
            }
        } else {
            $pubObj = new Publication($cPubId);
            $langId = (is_object($pubObj)
                            && $pubObj->exists()) ? $pubObj->getLanguageId() : '';
        }
        
        // gets the issue number
        if (!empty($cIssueSName)) {
            $issueArray = Issue::GetIssues($cPubId, $langId, $cIssueSName);
            if (sizeof($issueArray) == 1) {
                $issueObj = $issueArray[0];
                $issueNr = (is_object($issueObj)
                                && $issueObj->exists) ? $issueObj->getIssueNumber() : '';
            }
        } else {
            $query = "SELECT MAX(Number) FROM Issues"
                   . " WHERE IdPublication = ".$cPubId." AND IdLanguage = ".$cLangCode
                   . " AND ShortName = '".$cIssueSName."'";
            $data = $g_ado_db->GetRow($query);
            $issueNr = $data['Number'];
        }
        
        // gets the section number
        if (!empty($cSectionSName)) {
            $sectionArray = Section::GetSections($cPubId, $issueNr, $langId, $cSectionName);
            if (sizeof($sectionArray) == 1) {
                $sectionObj = $sectionArray[0];
                $sectionNr = (is_object($sectionObj)
                                && $sectionObj->exists()) ? $sectionObj->getSectionNumber() : '';
            }
        }
        
        // gets the article number
        if (!empty($cArticleSName)) {
            $articleObj = new Article($langId, $cArticleSName);
            $articleNr = (is_object($articleObj)
                            && $articleObj->exists()) ? $articleObj->getArticleNumber() : '';
        }
        
    } // fn parser


	/**
     * Translates a regular URL into URL shortnames fashion.
     *
	 * @return string $uriPath
	 *      The shortnames version of the URI
	 */
	public static function BuildURI()
	{
		// gets the parameters identifiers from the URI query variables
		$uriObj = CampURI::singleton();
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
			$issueShortName = $issueObj->getUrlName();
		}
		
		// gets the section short name
		$sectionObj = new Section($cPubId, $cIssueNr, $cLangId, $cSectionNr);
		if (is_object($sectionObj) && $sectionObj->exists()) {
			$sectionShortName = $sectionObj->getUrlName();
		}
		
		// gets the article short name
		$articleObj = new Article($cLangId, $cArticleNr);
		if (is_object($articleObj) && $articleObj->exists()) {
			$articleShortName = $articleObj->getUrlName();
		}
        
        $uriPath = '/'.$langCode.'/'.$issueShortName.'/'.$sectionShortName.'/'.$articleShortName.'/';
        
        return $uriPath;
	} // fn BuildURI

} // class CampURIShortNames

?>