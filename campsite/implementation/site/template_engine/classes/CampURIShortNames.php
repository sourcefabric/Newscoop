<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
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
     * Parameters that are restricted to CampURIShortNames object use.
     *
     * @var array
     */
    static private $m_restrictedParameters = array('NrImage', 'IdLanguage',
    'IdPublication', 'NrIssue', 'NrSection', 'NrArticle', 'subtitle', 'ILStart',
    'SLStart', 'ALStart', 'SrLStart', 'StLStart', 'class', 'cb_subs', 'tx_subs',
    'subscribe', 'useradd', 'usermodify', 'login', 'SubsType', 'keyword', 'search',
    'RememberUser', 'tpid', 'tpl', 'preview', 'debug'
    );

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
            return $this->m_template->name;
        }

        $templateId = CampRequest::GetVar(CampRequest::TEMPLATE_ID);
        if (!empty($templateId)) {
            $tplObj = new Template($templateId);
            if (!is_object($tplObj) || !$tplObj->exists()) {
                return null;
            }
            $template = $tplObj->getName();
        } else {
            $languageId = !is_null($this->language) && $this->language->defined ?
            $this->language->number : null;
            $publicationId = !is_null($this->publication) && $this->publication->defined ?
            $this->publication->identifier : null;
            $issueNo = !is_null($this->issue) && $this->issue->defined ?
            $this->issue->number : null;
            $sectionNo = !is_null($this->section) && $this->section->defined ?
            $this->section->number : null;
            $articleNo = !is_null($this->article) && $this->article->defined ?
            $this->article->number : null;
            $template = CampSystem::GetTemplate($languageId, $publicationId, $issueNo,
                                                $sectionNo, $articleNo);
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
     * @return array
     *      An array containing all the form parameters to print out
     */
    public function getFormParameters()
    {
        $baseParameters = array('IdLanguage','IdPublication',
                                'NrIssue','NrSection','NrArticle');
        $parameters = array();
        $i = 0;
        $context = CampTemplate::singleton()->context();
        if ($context->language->defined == true) {
            $parameters[$i++] = array('name' => 'IdLanguage',
                                      'value' => $context->language->number);
        }
        if ($context->publication->defined == true) {
            $parameters[$i++] = array('name' => 'IdPublication',
                                      'value' => $context->publication->identifier);
        }
        if ($context->issue->defined == true) {
            $parameters[$i++] = array('name' => 'NrIssue',
                                      'value' => $context->issue->number);
        }
        if ($context->section->defined == true) {
            $parameters[$i++] = array('name' => 'NrSection',
                                      'value' => $context->section->number);
        }
        if ($context->article->defined == true) {
            $parameters[$i++] = array('name' => 'Article',
                                      'value' => $context->article->number);
        }

        $queryParameters = $this->getQueryArray();
        foreach ($queryParameters as $paramName => $paramValue) {
            if (in_array($paramName, $baseParameters)) {
                continue;
            }
            $parameters[$i]['name'] = $paramName;
            $parameters[$i++]['value'] = $paramValue;
        }

        return $parameters;
    } // fn getFormParameters

    
    /**
     * Returns true if the given parameter is restricted and can not 
     * be set from outside the URL object.
     *
     * @param string $p_parameterName
     * @return bool
     */
    public function isRestrictedParameter($p_parameterName)
    {
        return in_array($p_parameterName, CampURIShortNames::$m_restrictedParameters);
    }
    

    /**
     * Sets the URL values.
     *
     * @return void
     *
     * TODO: Error handling
     */
    private function setURL()
    {
        $this->m_publication = null;
        // gets the publication object based on site name (URI host)
        $alias = preg_replace('/^'.$this->getScheme().':\/\//', '', $this->getBase());
        $aliasArray = Alias::GetAliases(null, null, $alias);
        if (is_array($aliasArray) && sizeof($aliasArray) == 1) {
            $this->m_publication = new MetaPublication($aliasArray[0]->getPublicationId());
        }
        if (is_null($this->m_publication) || !$this->m_publication->defined()) {
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
            if (is_null($articleObj) || !$articleObj->exists()) {
                CampTemplate::singleton()->trigger_error('not valid article');
                return;
            }
            $this->m_article = new MetaArticle($this->m_language->number,
                                               $articleObj->getArticleNumber());
        }

        $this->m_template = new MetaTemplate($this->getTemplate());
        $this->m_validURI = true;
        $this->validateCache(false);
    } // fn setURL


	/**
     * Sets the URI path and query values based on given parameters.
     *
     * @param string $p_param
     *      A valid URL parameter
     *
	 * @return void
	 */
	protected function buildURI($p_param = null)
	{
	    if ($this->isValidCache()) {
	        return;
	    }

        $this->m_uriPath = null;
        $this->m_uriQuery = null;
        
        if (!is_null($p_param)) {
            $params = preg_split('/ /', $p_param);
            $parameter = $params[0];
            $option = isset($params[1]) ? $params[1] : null;
        } else {
            $parameter = null;
            $option = null;
        }

        switch($parameter) {
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
            if ($context->attachment->defined) {
                $attachment = new Attachment($context->attachment->identifier);
                $this->m_uriPath = '/attachment/'.basename($attachment->getStorageLocation());
            }
            break;
        case 'image':
            $context = CampTemplate::singleton()->context();
            if (!is_null($option)) {
                $oldImage = $context->image;
                $context->image = new MetaImage($option);
            }
            if ($context->image->article_index !== null) {
                $this->m_uriPath = '/get_img';
                $this->m_uriQuery = 'NrImage=' . $context->image->article_index
                . '&amp;NrArticle=' . $context->article->number;
            }
            if (!is_null($option)) {
                $context->image = $oldImage;
            }
            break;
        case 'template':
            $template = new MetaTemplate($option);
            if ($template->defined) {
                $oldTplId = $this->getQueryVar(CampRequest::TEMPLATE_ID);
                $this->setQueryVar(CampRequest::TEMPLATE_ID, $template->identifier);
                $this->buildURI();
                $this->setQueryVar(CampRequest::TEMPLATE_ID, $oldTplId);
            }
            break;
        default:
            if (empty($p_param)) {
                if (!is_null($this->m_language) && $this->m_language->defined) {
                    $this->m_path = '/' . $this->m_language->code . '/';
                    if (!is_null($this->m_issue) && $this->m_issue->defined) {
                        $this->m_path .= $this->m_issue->url_name . '/';
                        if (!is_null($this->m_section) && $this->m_section->defined) {
                            $this->m_path .= $this->m_section->url_name . '/';
                            if (!is_null($this->m_article) && $this->m_article->defined) {
                                $this->m_path .= $this->m_article->url_name . '/';
                            }
                        }
                    }
                }
                $this->m_uriPath = $this->m_path;
                $this->m_query = CampURI::QueryArrayToString($this->getQueryArray());
                $this->m_uriQuery = $this->m_query;
            }
        }
        $this->validateCache(true);
	} // fn buildURI

} // class CampURIShortNames

?>