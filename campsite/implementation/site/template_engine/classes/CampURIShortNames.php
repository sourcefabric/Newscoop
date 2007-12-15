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
     *
     * @var bool
     */
    private $m_validCache = false;


	/**
	 * Class constructor
     *
     * @param string $p_uri
     *      The full URI string
	 */
	public function __construct($p_uri = null)
	{
        parent::__construct($p_uri);

        $this->setURLType(URLTYPE_SHORT_NAMES);
        $this->setURL();
	} // fn __construct


    /**
     * Gets the Language code from the URI.
     *
     * @return string
     *      The language code
     */
    public function getLanguageCode()
    {
        return is_null($this->m_language) ? null : $this->m_language->getCode();
    } // fn getLanguageCode


    /**
     * Gets the Publication alias from the URI.
     *
     * @return string
     *      The publication alias (a.k.a. site name)
     */
    public function getPublicationAlias()
    {
        if (is_null($this->m_publication)) {
            return null;
        }
        $defaultAlias = new Alias($this->m_publication->getDefaultAliasId());
        return $defaultAlias->getName();
    } // fn getPublicationAlias


    /**
     * Gets the Issue short name from the URI.
     *
     * @return string
     *      The short name of the issue
     */
    public function getIssueShortName()
    {
        return is_null($this->m_issue) ? null : $this->m_issue->getUrlName();
    } // fn getIssueShortName


    /**
     * Gets the Section short name from the URI.
     *
     * @return string
     *      The short name of the section
     */
    public function getSectionShortName()
    {
        return is_null($this->m_section) ? null : $this->m_section->getUrlName();
    } // fn getSectionShortName


    /**
     * Gets the Article short name from the URI.
     *
     * @return string
     *      The short name of the Article
     */
    public function getArticleShortName()
    {
        return is_null($this->m_article) ? null : $this->m_article->getUrlName();
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
        if (!is_null($this->m_language)) {
            $uriString = '/' . $this->m_language->code . '/';
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

        if (!is_null($this->m_issue)) {
            $uriString .= $this->m_issue->url_name . '/';
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

        if (!is_null($this->m_section)) {
            $uriString .= $this->m_section->url_name . '/';
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

        if (!is_null($this->m_article)) {
            $uriString .= $this->m_article->url_name . '/';
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

        $this->m_publication = null;
        // gets the publication object based on site name (URI host)
        $alias = ltrim($this->getBase(), $this->getScheme().'://');
        $aliasArray = Alias::GetAliases(null, null, $alias);
        if (is_array($aliasArray) && sizeof($aliasArray) == 1) {
            $this->m_publication = new MetaPublication($aliasArray[0]->getPublicationId());
        }
        if (is_null($this->m_publication) || !$this->m_publication->defined()) {
            CampTemplate::singleton()->trigger_error('not valid site alias');
            return;
        }
        $this->setQueryVar(CampRequest::PUBLICATION_ID, $this->m_publication->identifier, false);

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

        $this->m_language = null;
        // gets the language identifier and sets the language code
        if (!empty($cLangCode)) {
            $langArray = Language::GetLanguages(null, $cLangCode);
            if (is_array($langArray) && sizeof($langArray) == 1) {
                $this->m_language = new MetaLanguage($langArray[0]->getLanguageId());
            }
        } else {
            $this->m_language = new MetaLanguage($this->m_publication->default_language->number);
        }

        if (is_null($this->m_language) || !$this->m_language->defined()) {
            CampTemplate::singleton()->trigger_error('not valid language');
            return;
        }
        $this->setQueryVar(CampRequest::LANGUAGE_ID, $this->m_language->number, false);

        $this->m_issue = null;
        // gets the issue number and sets the issue short name
        if (!empty($cIssueSName)) {
            $issueArray = Issue::GetIssues($this->m_publication->identifier,
                                           $this->m_language->number,
                                           null, $cIssueSName);
            if (is_array($issueArray) && sizeof($issueArray) == 1) {
                $this->m_issue = new MetaIssue($this->m_publication->identifier,
                                               $this->m_language->number,
                                               $issueArray[0]->getIssueNumber());
            }
        } else {
            $issueObj = Issue::GetCurrentIssue($this->m_publication->identifier,
                                               $this->m_language->number);
            $this->m_issue = new MetaIssue($this->m_publication->identifier,
                                           $this->m_language->number,
                                           $issueObj->getIssueNumber());
        }
        if (is_null($this->m_issue) || !$this->m_issue->defined()) {
            CampTemplate::singleton()->trigger_error('not valid issue');
            return;
        }
        $this->setQueryVar(CampRequest::ISSUE_NR, $this->m_issue->number, false);

        $this->m_section = null;
        // gets the section number and sets the section short name
        if (!empty($cSectionSName)) {
            $sectionArray = Section::GetSections($this->m_publication->identifier,
                                                 $this->m_issue->number,
                                                 $this->m_language->number,
                                                 $cSectionSName);
            if (is_array($sectionArray) && sizeof($sectionArray) == 1) {
                $this->m_section = new MetaSection($this->m_publication->identifier,
                                                   $this->m_issue->number,
                                                   $this->m_language->number,
                                                   $sectionArray[0]->getSectionNumber());
            }
            if (is_null($this->m_section) || !$this->m_section->defined()) {
                CampTemplate::singleton()->trigger_error('not valid section');
                return;
            }
            $this->setQueryVar(CampRequest::SECTION_NR, $this->m_section->number, false);
        }

        $this->m_article = null;
        // gets the article number and sets the article short name
        if (!empty($cArticleSName)) {
            // we pass article short name as article identifier as they are
            // the same for Campsite, we will have to change this in the future
            $articleObj = Article::GetByNumber($cArticleSName,
                                               $this->m_publication->identifier,
                                               $this->m_issue->number,
                                               $this->m_section->number,
                                               $this->m_language->number);
            $this->m_article = new MetaArticle($this->m_language->number,
                                               $articleObj->getArticleNumber());
            if (is_null($this->m_article) || !$this->m_article->defined()) {
                CampTemplate::singleton()->trigger_error('not valid article');
                return;
            }
            $this->setQueryVar(CampRequest::ARTICLE_NR, $this->m_article->number, false);
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
                if (!is_null($this->m_language)) {
                    $this->m_path = '/' . $this->m_language->code . '/';
                    if (!is_null($this->m_issue)) {
                        $this->m_path .= $this->m_issue->url_name . '/';
                        if (!is_null($this->m_section)) {
                            $this->m_path .= $this->m_section->url_name . '/';
                            if (!is_null($this->m_article)) {
                                $this->m_path .= $this->m_article->url_name . '/';
                            }
                        }
                    }
                }
                $this->m_uriPath = $this->m_path;
                $this->m_uriQuery = $this->m_query;
            }
        }
	} // fn buildURI


    /**
     * Sets the cache validation for URI rendering
     *
     * @param bool $p_valid
     */
    protected function validateCache($p_valid)
    {
        $this->m_validCache = $p_valid;
    }

} // class CampURIShortNames

?>