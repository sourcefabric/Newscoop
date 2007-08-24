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

define('UP_LANGUAGE_ID', 'IdLanguage');
define('UP_PUBLICATION_ID', 'IdPublication');
define('UP_ISSUE_NR', 'NrIssue');
define('UP_SECTION_NR', 'NrSection');
define('UP_ARTICLE_NR', 'NrArticle');

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
        $this->setURL();
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
     * Sets the URL values.
     *
     * @return void
     *
     * TODO: Error handling
     */
    private function setURL()
    {
        // gets the publication object based on site name (URI host)
        $aliasArray = Alias::GetAliases(null, null, $this->getHost());
        if (is_array($aliasArray) && sizeof($aliasArray) == 1) {
            $aliasObj = $aliasArray[0];
            $cPubId = $aliasObj->getPublicationId();
            $pubObj = new Publication($cPubId);
            if (!is_object($pubObj) || !$pubObj->exists()) {
                $cPubId = 0;
                $pubObj = null;
            }
            $this->setQueryVar(UP_PUBLICATION_ID, $cPubId);
        }

        if (empty($cPubId)) {
            // return error/throw exception "not valid site alias"
        }

        $trimmedPath = trim($this->getPath(), '/');
        list($cLangCode, $cIssueSName,
             $cSectionSName, $cArticleSName) = explode('/', $trimmedPath);

        // gets the language identifier
        if (!empty($cLangCode)) {
            $langArray = Language::GetLanguages(null, $cLangCode);
            if (is_array($langArray) && sizeof($langArray) == 1) {
                $langObj = $langArray[0];
                $cLangId = $langObj->getLanguageId();
            }
        } else {
            $cLangId = $pubObj->getLanguageId();
        }

        if (empty($cLangId)) {
            // return error/throw exception "not valid language"
        }

        if ($this->getPath() == '' || $this->getPath() == '/') {
            $this->setQueryVar(UP_LANGUAGE_ID, $cLangId);
        }
        
        // gets the issue number
        if (!empty($cIssueSName)) {
            $issueArray = Issue::GetIssues($cPubId, $cLangId, $cIssueSName);
            if (is_array($issueArray) && sizeof($issueArray) == 1) {
                $issueObj = $issueArray[0];
                $cIssueNr = $issueObj->getIssueNumber();
            }
            if (empty($cIssueNr)) {
                // return error/throw exception "not valid issue"
            }
            $this->setQueryVar(UP_ISSUE_NR, $cIssueNr);
        } elseif ($this->getPath() == '' || $this->getPath() == '/') {
            $query = "SELECT MAX(Number) FROM Issues"
                   . " WHERE IdPublication = ".$cPubId." AND IdLanguage = ".$cLangId
                   . " AND ShortName = '".$cIssueSName."'";
            $data = $g_ado_db->GetRow($query);
            if (empty($data)) {
                // return error/throw exception "not issues at all"
            }
            $cIssueNr = $data['Number'];
        }

        if (!empty($cIssueNr)) {
            $this->setQueryVar(UP_ISSUE_NR, $cIssueNr);
        }
        
        // gets the section number
        if (!empty($cSectionSName)) {
            $sectionArray = Section::GetSections($cPubId, $issueNr, $cLangId, $cSectionSName);
            if (is_array($sectionArray) && sizeof($sectionArray) == 1) {
                $sectionObj = $sectionArray[0];
                $cSectionNr = $sectionObj->getSectionNumber();
                $this->setQueryVar(UP_SECTION_NR, $cSectionNr);
            }
            
            if (empty($cSectionNr)) {
                // return error/throw exception "not valid section"
            }
        }
        
        // gets the article number
        if (!empty($cArticleSName)) {
            $articleObj = new Article($cLangId, $cArticleSName);
            if (is_object($articleObj) && $articleObj->exists()) {
                $cArticleNr = $articleObj->getArticleNumber();
                $this->setQueryVar(UP_ARTICLE_NR, $cArticleNr);
            }

            if (empty($cArticleNr)) {
                // return error/throw exception "not valid article"
            }
        }
    } // fn setURL


	/**
     * Translates a regular URL into URL shortnames fashion.
     *
	 * @return string $uriPath
	 *      The shortnames version of the URI
	 */
	public function buildURI()
	{
        $uriPath = '';
        
        // gets the language code
		$langObj = new Language($this->getQueryVar(UP_LANGUAGE_ID);
		if (is_object($langObj) && $langObj->exists()) {
			$langCode = $langObj->getCode();
		}
		
		// gets the issue short name
		$issueObj = new Issue($this->getQueryVar(UP_PUBLICATION_ID),
                              $this->getQueryVar(UP_LANGUAGE_ID),
                              $this->getQueryVar(UP_ISSUE_NR));
		if (is_object($issueObj) && $issueObj->exists()) {
			$issueSName = $issueObj->getUrlName();
		}
		
		// gets the section short name
		$sectionObj = new Section($this->getQueryVar(UP_PUBLICATION_ID),
                                  $this->getQueryVar(UP_ISSUE_NR),
                                  $this->getQueryVar(UP_LANGUAGE_ID),
                                  $this->getQueryVar(UP_SECTION_NR));
		if (is_object($sectionObj) && $sectionObj->exists()) {
			$sectionSName = $sectionObj->getUrlName();
		}
		
		// gets the article short name
		$articleObj = new Article($this->getQueryVar(UP_LANGUAGE_ID),
                                  $this->getQueryVar(UP_ARTICLE_NR));
		if (is_object($articleObj) && $articleObj->exists()) {
			$articleSName = $articleObj->getUrlName();
		}
        
        if (!empty($langCode) && !empty($issueSName)
                && !empty($sectionSName) && !empty($articleSName)) {
            $uriPath = '/'.$langCode.'/'.$issueSName.'/'.$sectionSName.'/'.$articleSName.'/';
        }

        return $uriPath;
	} // fn buildURI

} // class CampURIShortNames

?>