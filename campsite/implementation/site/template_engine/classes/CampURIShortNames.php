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
require_once($g_documentRoot.'/template_engine/classes/CampRequest.php');
require_once($g_documentRoot.'/template_engine/classes/CampURI.php');
require_once($g_documentRoot.'/template_engine/classes/CampTemplate.php');

/**
 * Class CampURIShortNames
 */
class CampURIShortNames extends CampURI
{
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
     * @var integer
     */
    private $m_publicationId = null;

    /**
     * Language code
     *
     * @var string
     */
    private $m_language = null;

    /**
     * @var integer
     */
    private $m_languageId = null;

    /**
     * Issue short name
     *
     * @var string
     */
    private $m_issue = null;

    /**
     * @var integer
     */
    private $m_issueNr = null;

    /**
     * Section short name
     *
     * @var string
     */
    private $m_section = null;

    /**
     * @var integer
     */
    private $m_sectionNr = null;

    /**
     * Article short name
     *
     * @var string
     */
    private $m_article = null;

    /**
     * @var integer
     */
    private $m_articleNr = null;

    /**
     * @var string
     */
    private $m_template = null;

    /**
     * Holds the URI path from buildURI() method.
     *
     * @var string
     */
    private $m_uriPath = null;

    /**
     * Holds the URI query from buildURI() method.
     *
     * @var string
     */
    private $m_uriQuery = null;

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
     *
     */
    public function getTemplate()
    {
        if (!is_null($this->m_template)) {
            return $this->m_template;
        }

        $templateId = $this->getQueryVar(CampRequest::TEMPLATE_ID);
        if (!empty($templateId)) {
            $tplObj = new Template($templateId);
            if (!is_object($tplObj) || !$tplObj->exists()) {
                return null;
            }
            $template = $tplObj->getName();
        } else {
            $template = CampSystem::GetTemplate($this->getQueryVar(CampRequest::LANGUAGE_ID),
                                                $this->getQueryVar(CampRequest::PUBLICATION_ID),
                                                $this->getQueryVar(CampRequest::ISSUE_NR),
                                                $this->getQueryVar(CampRequest::SECTION_NR),
                                                $this->getQueryVar(CampRequest::ARTICLE_NR));
        }

        return $template;
    } // fn getTemplate


    /**
     * Gets the language URI path.
     *
     * @return string
     *      The language URI path
     */
    private function getURILanguage()
    {
        $uriString = null;
        if (!empty($this->m_language)) {
            $uriString = '/' . $this->m_language . '/';
        }

        return $uriString;
    } // fn getURILanguage


    /**
     * Gets the issue URI path.
     * It fetches the issue URL name from URI or current issue list if any.
     *
     * @return string
     *      The issue URI path
     */
    private function getURIIssue()
    {
        $uriString = $this->getURILanguage();
        if (is_null($uriString)) {
            return null;
        }

        if (!empty($this->m_issue)) {
            $uriString .= $this->m_issue . '/';
        } else {
            $uriString = null;
        }

        return $uriString;
    } // fn getURIIssue


    /**
     * Gets the section URI path.
     * It fetches the section URL name from URI or current section list if any.
     *
     * @return string
     *      The section URI path
     */
    private function getURISection()
    {
        $uriString = $this->getURIIssue();
        if (is_null($uriString)) {
            return null;
        }

        if (!empty($this->m_section)) {
            $uriString .= $this->m_section . '/';
        } else {
            $uriString = null;
        }

        return $uriString;
    } // fn getURISection


    /**
     * Gets the article URI path.
     * It fetches the article URL name from URI or current article list if any.
     *
     * @return string
     *      The article URI path
     */
    private function getURIArticle()
    {
        $uriString = $this->getURISection();

        if (!empty($this->m_article)) {
            $uriString .= $this->m_article . '/';
        } else {
            $uriString = null;
        }

        return $uriString;
    } // fn getURIArticle


    /**
     * Returns the URI string based on given URL parameter.
     *
     * @param string $p_param
     *      The URL parameter
     *
     * @return string
     *      The URI string requested
     */
    public function getURI($p_param = null)
    {
        if (!$this->m_validURI) {
            return null;
        }

        $this->buildURI($p_param);
        if (!empty($this->m_uriQuery)) {
            return $this->m_uriPath . '?' . $this->m_uriQuery;
        }

        return $this->m_uriPath;
    } // fn getURI


    /**
     * Returns the URI path based on given URL parameter.
     *
     * @param string $p_param
     *      The URL parameter
     *
     * @return string
     *      The URI path string requested
     */
    public function getURIPath($p_param = null)
    {
        if (!$this->m_validURI) {
            return null;
        }

        $this->buildURI($p_param);
        return $this->m_uriPath;
    } // fn getURIPath


    /**
     * Returns the URI query parameters based on given URL parameter.
     *
     * @param string $p_param
     *
     * @return string
     *      The URI query string requested
     */
    public function getURLParameters($p_param = null)
    {
        if (!$this->m_validURI) {
            return null;
        }

        $this->buildURI($p_param);
        return $this->m_uriQuery;
    } // fn getURLParameters


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
                $this->setQueryVar(CampRequest::PUBLICATION_ID, $cPubId, false);
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
        $cParams = explode('/', trim($this->getPath(), '/'));
        $cParamsSize = sizeof($cParams);
        if ($cParamsSize >= 1) {
            $cLangCode = $cParams[0];
        }
        if ($cParamsSize >= 2) {
            $cIssueSName = $cParams[1];
        }
        if ($cParamsSize >= 3) {
            $cSectionSName = $cParams[2];
        }
        if ($cParamsSize == 4) {
            $cArticleSName = $cParams[3];
        }

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
            $this->setQueryVar(CampRequest::LANGUAGE_ID, $cLangId, false);
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
            $this->setQueryVar(CampRequest::ISSUE_NR, $cIssueNr, false);
            $this->m_issue = $cIssueSName;
        }

        $cSectionNr = 0;
        // gets the section number and sets the section short name
        if (!empty($cSectionSName)) {
            $sectionArray = Section::GetSections($cPubId, $cIssueNr, $cLangId, $cSectionSName);
            if (is_array($sectionArray) && sizeof($sectionArray) == 1) {
                $sectionObj = $sectionArray[0];
                $cSectionNr = $sectionObj->getSectionNumber();
                $this->setQueryVar(CampRequest::SECTION_NR, $cSectionNr, false);
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
                $this->setQueryVar(CampRequest::ARTICLE_NR, $cArticleNr, false);
                $this->m_article = $cArticleSName;
            }

            if (empty($cArticleNr)) {
                CampTemplate::singleton()->trigger_error('not valid article');
                return;
            }
        }

        $this->m_template = $this->getTemplate();
        $this->m_validURI = true;
    } // fn setURL


	/**
     * Sets the URI path and query values based on given parameters.
     *
     * @param string $p_param
     *      A valid URL parameter
     *
	 * @return void
	 */
	private function buildURI($p_param = null)
	{
        $this->m_uriPath = null;
        $this->m_uriQuery = null;

        switch($p_param) {
        case 'language':
        case 'publication':
            $this->m_uriPath = $this->getURILanguage();
            break;
        case 'issue':
            $this->m_uriPath = $this->getURIIssue();
            break;
        case 'section':
            $this->m_uriPath = $this->getURISection();
            break;
        case 'article':
            $this->m_uriPath = $this->getURIArticle();
            break;
        case 'articleattachment':
            $context = CampTemplate::singleton()->context();
            $attachment = new Attachment($context->attachment->identifier);
            $this->m_uriPath = '/attachment/'.basename($attachment->getStorageLocation());
            break;
        case 'image':
            $context = CampTemplate::singleton()->context();
            $image = '';
            $this->m_uriPath = '';
            $this->m_uriQuery = 'NrImage=' . '&amp;NrArticle='.$context->article->number;
            break;
        default:
            if (empty($p_param)) {
                $this->m_uriPath = $this->m_path;
                $this->m_uriQuery = $this->m_query;
            }
        }
	} // fn buildURI


    /**
     *
     */
    public function setPublicationId($p_identifier)
    {
        $this->m_publicationId = $p_identifier;
    } // fn setPublicationId


    /**
     *
     */
    public function setLanguageId($p_identifier)
    {
        $this->m_languageId = $p_identifier;
    } // fn setLanguageId


    /**
     *
     */
    public function setIssueNr($p_number)
    {
        $this->m_issueNr = $p_number;
    } // fn setIssueNr


    /**
     *
     */
    public function setSectionNr($p_number)
    {
        $this->m_sectionNr = $p_number;
    } // fn setSectionNr


    /**
     *
     */
    public function setArticleNr($p_number)
    {
        $this->m_articleNr = $p_number;
    } // fn setSectionNr

} // class CampURIShortNames

?>