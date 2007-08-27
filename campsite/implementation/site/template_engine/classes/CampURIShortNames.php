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
     * Publication alias (a.k.a. site name)
     *
     * @var string
     */
    private $m_publication = null;

    /**
     * Language code
     *
     * @var string
     */
    private $m_language = null;

    /**
     * Issue short name
     *
     * @var string
     */
    private $m_issue = null;

    /**
     * Section short name
     *
     * @var string
     */
    private $m_section = null;

    /**
     * Article short name
     *
     * @var string
     */
    private $m_article = null;

    /**
     * Whether the URI is valid or not
     *
     * @var boolean
     */
    private $m_validURI = false;


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
     * Gets the Publication alias from the URI.
     *
     * @return string
     *      The publication alias (a.k.a. site name)
     */
    public function getPublicationAlias()
    {
        return $this->m_publication;
    } // fn getPublicationAlias


    /**
     * Gets the Language code from the URI.
     *
     * @return string
     *      The language code
     */
    public function getLanguageCode()
    {
        return $this->m_language;
    } // fn getLanguageCode


    /**
     * Gets the Issue short name from the URI.
     *
     * @return string
     *      The short name of the issue
     */
    public function getIssueShortName()
    {
        return $this->m_issue;
    } // fn getIssueShortName


    /**
     * Gets the Section short name from the URI.
     *
     * @return string
     *      The short name of the section
     */
    public function getSectionShortName()
    {
        return $this->m_section;
    } // fn getSectionShortName


    /**
     * Gets the Article short name from the URI.
     *
     * @return string
     *      The short name of the Article
     */
    public function getArticleShortName()
    {
        return $this->m_article;
    } // fn getArticleShortName


    /**
     * Sets the URL values.
     *
     * @return void
     *
     * TODO: Error handling
     */
    private function setURL()
    {
        $cPubId = 0;
        // gets the publication object based on site name (URI host)
        $aliasArray = Alias::GetAliases(null, null, $this->getHost());
        if (is_array($aliasArray) && sizeof($aliasArray) == 1) {
            $aliasObj = $aliasArray[0];
            $cPubId = $aliasObj->getPublicationId();
            $pubObj = new Publication($cPubId);
            if (is_object($pubObj) && $pubObj->exists()) {
                $this->setQueryVar(UP_PUBLICATION_ID, $cPubId);
                $this->m_publication = $this->getHost();
            } else {
                $cPubId = 0;
                $pubObj = null;
            }
        }

        if (empty($cPubId)) {
            // return error/throw exception "not valid site alias"
        }

        $trimmedPath = trim($this->getPath(), '/');
        list($cLangCode, $cIssueSName,
             $cSectionSName, $cArticleSName) = explode('/', $trimmedPath);

        $cLangId = 0;
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
        } else {
            $this->m_language = $cLangCode;
        }

        if ($this->getPath() == '' || $this->getPath() == '/') {
            $this->setQueryVar(UP_LANGUAGE_ID, $cLangId);
        }

        $cIssueNr = 0;
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
        } elseif ($this->getPath() == '' || $this->getPath() == '/') {
            $query = "SELECT MAX(Number), ShortName FROM Issues"
                   . " WHERE IdPublication = ".$cPubId." AND IdLanguage = ".$cLangId
                   . " AND Published = 'Y'";
            $data = $g_ado_db->GetRow($query);
            if (empty($data)) {
                // return error/throw exception "not issues at all"
            }
            $cIssueNr = $data['Number'];
            $cIssueSName = $data['ShortName'];
        }

        if (!empty($cIssueNr)) {
            $this->setQueryVar(UP_ISSUE_NR, $cIssueNr);
            $this->m_issue = $cIssueSName;
        }

        $cSectionNr = 0;
        // gets the section number
        if (!empty($cSectionSName)) {
            $sectionArray = Section::GetSections($cPubId, $issueNr, $cLangId, $cSectionSName);
            if (is_array($sectionArray) && sizeof($sectionArray) == 1) {
                $sectionObj = $sectionArray[0];
                $cSectionNr = $sectionObj->getSectionNumber();
                $this->setQueryVar(UP_SECTION_NR, $cSectionNr);
                $this->m_section = $cSectionSName;
            }

            if (empty($cSectionNr)) {
                // return error/throw exception "not valid section"
            }
        }

        $cArticleNr = 0;
        // gets the article number
        if (!empty($cArticleSName)) {
            $articleObj = new Article($cLangId, $cArticleSName);
            if (is_object($articleObj) && $articleObj->exists()) {
                $cArticleNr = $articleObj->getArticleNumber();
                $this->setQueryVar(UP_ARTICLE_NR, $cArticleNr);
                $this->m_article = $cArticleSName;
            }

            if (empty($cArticleNr)) {
                // return error/throw exception "not valid article"
            }
        }

        $this->m_validURI = true;
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
        
        if ($this->m_validURI == true) {
            $uriPath = '/'.$this->m_language.'/'.$this->m_issue.'/';
            $uriPath.= (!empty($this->m_section)) ? $this->m_section.'/' : '';
            $uriPath.= (!empty($this->m_article)) ? $this->m_article.'/' : '';
        }

        return $uriPath;
	} // fn buildURI

} // class CampURIShortNames

?>