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
require_once($g_documentRoot.'/template_engine/classes/CampTemplate.php');

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
        $this->setURLType(URLTYPE_SHORT_NAMES);
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
        global $g_ado_db;

        $cPubId = 0;
        // gets the publication object based on site name (URI host)
        $alias = ltrim($this->getBase(), $this->getScheme().'://');
        $aliasArray = Alias::GetAliases(null, null, $alias);
        if (is_array($aliasArray) && sizeof($aliasArray) == 1) {
            $aliasObj = $aliasArray[0];
            $cPubId = $aliasObj->getPublicationId();
            $pubObj = new Publication($cPubId);
            if (is_object($pubObj) && $pubObj->exists()) {
                $this->setQueryVar(UP_PUBLICATION_ID, $cPubId, false);
                $this->m_publication = $aliasObj->getName();
            } else {
                $cPubId = 0;
                $pubObj = null;
            }
        }

        if (empty($cPubId)) {
            CampTemplate::singleton()->trigger_error('not valid site alias');
            return;
        }

        // reads parameters values if any
        $trimmedPath = trim($this->getPath(), '/');
        list($cLangCode, $cIssueSName,
             $cSectionSName, $cArticleSName) = explode('/', $trimmedPath);

        $cLangId = 0;
        // gets the language identifier and sets the language code
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
            CampTemplate::singleton()->trigger_error('not valid language');
            return;
        } else {
            $this->setQueryVar(UP_LANGUAGE_ID, $cLangId, false);
            if (empty($cLangCode)) {
                $langObj = new Language($cLangId);
                if (is_object($langObj) && $langObj->exists()) {
                    $cLangCode = $langObj->getCode();
                }
            }
            $this->m_language = $cLangCode;
        }

        $cIssueNr = 0;
        // gets the issue number and sets the issue short name
        if (!empty($cIssueSName)) {
            $issueArray = Issue::GetIssues($cPubId, $cLangId, null, $cIssueSName);
            if (is_array($issueArray) && sizeof($issueArray) == 1) {
                $issueObj = $issueArray[0];
                $cIssueNr = $issueObj->getIssueNumber();
            }
            if (empty($cIssueNr)) {
                CampTemplate::singleton()->trigger_error('not valid issue');
                return;
            }
        } else {
            $query = "SELECT Number, ShortName FROM Issues "
                   . "WHERE IdPublication = ".$cPubId
                         ." AND IdLanguage = ".$cLangId
                         ." AND Published = 'Y' ORDER BY Number DESC LIMIT 1";
            $data = $g_ado_db->GetRow($query);
            if (empty($data)) {
                CampTemplate::singleton()->trigger_error('not published issues');
                return;
            }
            $cIssueNr = $data['Number'];
            $cIssueSName = $data['ShortName'];
        }

        if (!empty($cIssueNr)) {
            $this->setQueryVar(UP_ISSUE_NR, $cIssueNr, false);
            $this->m_issue = $cIssueSName;
        }

        $cSectionNr = 0;
        // gets the section number and sets the section short name
        if (!empty($cSectionSName)) {
            $sectionArray = Section::GetSections($cPubId, $cIssueNr, $cLangId, $cSectionSName);
            if (is_array($sectionArray) && sizeof($sectionArray) == 1) {
                $sectionObj = $sectionArray[0];
                $cSectionNr = $sectionObj->getSectionNumber();
                $this->setQueryVar(UP_SECTION_NR, $cSectionNr, false);
                $this->m_section = $cSectionSName;
            }

            if (empty($cSectionNr)) {
                CampTemplate::singleton()->trigger_error('not valid section');
                return;
            }
        }

        $cArticleNr = 0;
        // gets the article number and sets the article short name
        if (!empty($cArticleSName)) {
            // we pass article short name as article identifier as they are
            // the same for Campsite, we will have to change this in the future
            $articleObj = Article::GetByNumber($cArticleSName, $cPubId,
                                               $cIssueNr, $cSectionNr, $cLangId);
            if (is_object($articleObj) && $articleObj->exists()) {
                $cArticleNr = $articleObj->getArticleNumber();
                $this->setQueryVar(UP_ARTICLE_NR, $cArticleNr, false);
                $this->m_article = $cArticleSName;
            }

            if (empty($cArticleNr)) {
                CampTemplate::singleton()->trigger_error('not valid article');
                return;
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
            $uriPath = '/';
            $uriPath.= (!empty($this->m_language)) ? $this->m_language.'/' : '';
            $uriPath.= (!empty($this->m_issue)) ? $this->m_issue.'/' : '';
            $uriPath.= (!empty($this->m_section)) ? $this->m_section.'/' : '';
            $uriPath.= (!empty($this->m_article)) ? $this->m_article.'/' : '';
        }

        return $uriPath;
	} // fn buildURI

} // class CampURIShortNames

?>