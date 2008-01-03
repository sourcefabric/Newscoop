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

require_once($g_documentRoot.'/classes/Template.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/classes/Publication.php');
require_once($g_documentRoot.'/classes/Issue.php');
require_once($g_documentRoot.'/classes/Section.php');
require_once($g_documentRoot.'/classes/Article.php');
require_once($g_documentRoot.'/classes/Alias.php');
require_once($g_documentRoot.'/template_engine/classes/CampURI.php');
require_once($g_documentRoot.'/template_engine/classes/CampTemplate.php');

/**
 * Class CampURITemplatePath
 */
class CampURITemplatePath extends CampURI
{
    /**
     * Parameters that are restricted to CampURITemplatePath object use.
     *
     * @var array
     */
    static private $m_restriectedParameters = array('NrImage', 'IdLanguage',
    'IdPublication', 'NrIssue', 'NrSection', 'NrArticle', 'subtitle', 'ILStart',
    'SLStart', 'ALStart', 'SrLStart', 'StLStart', 'class', 'cb_subs', 'tx_subs',
    'subscribe', 'useradd', 'usermodify', 'login', 'SubsType', 'keyword', 'search',
    'RememberUser', 'tpid', 'tpl', 'preview', 'debug'
    );
    
    /**
     * Template file name
     *
     * @var string
     */
    private $m_template = null;

    /**
     * Templates directory
     *
     * @var string
     */
    private $m_templatesPrefix = null;

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
     *      The requested URI
     */
    public function __construct($p_uri = null)
    {
        parent::__construct($p_uri);

        $this->setURLType(URLTYPE_TEMPLATE_PATH);
        $this->m_templatesPrefix = 'tpl';
        $this->parse();
        $this->setURL();
    } // fn __construct


    /**
     * Gets the current template name.
     *
     * @return string
     *      The name of the template
     */
    public function getTemplate()
    {
        if (!is_null($this->m_template)) {
            return $this->m_template;
        }

        $languageId = !is_null($this->language) ? $this->language->number : null;
        $publicationId = !is_null($this->publication) ? $this->publication->identifier : null;
        $issueNo = !is_null($this->issue) ? $this->issue->number : null;
        $sectionNo = !is_null($this->section) ? $this->section->number : null;
        $articleNo = !is_null($this->article) ? $this->article->number : null;
        $template = CampSystem::GetTemplate($languageId, $publicationId,
                                            $issueNo, $sectionNo, $articleNo);

        return $template;
    } // fn getTemplate


    /**
     * Gets the language URI query path.
     *
     * @return string
     *      The language URI query path
     */
    private function getURILanguage()
    {
        $uriString = '';

        if (!is_null($this->m_language)) {
            $uriString = CampRequest::LANGUAGE_ID.'='.$this->m_language->number;
        }

        return $uriString;
    } // fn getURILanguage


    /**
     * Gets the publication URI query path.
     * It fetches the publication URL name from URI context.
     *
     * @return string
     *      The publication URI query path
     */
    private function getURIPublication()
    {
        $uriString = $this->getURILanguage();
        if (empty($uriString)) {
            return null;
        }

        if (!is_null($this->m_publication)) {
            $uriString .= '&'.CampRequest::PUBLICATION_ID.'='.$this->m_publication->identifier;
        } else {
            $uriString = null;
        }

        return $uriString;
    } // fn getURIPublication


    /**
     * Gets the issue URI query path.
     * It fetches the issue URL name from URI or current issue list if any.
     *
     * @return string
     *      The issue URI query path
     */
    private function getURIIssue()
    {
        $uriString = $this->getURIPublication();
        if (empty($uriString)) {
            return null;
        }

        if (!is_null($this->m_issue)) {
            $uriString .= '&'.CampRequest::ISSUE_NR.'='.$this->m_issue->number;
        } else {
            $uriString = null;
        }

        return $uriString;
    } // fn getURIIssue


    /**
     * Gets the section URI query path.
     * It fetches the section URL name from URI or current section list if any.
     *
     * @return string
     *      The section URI query path
     */
    private function getURISection()
    {
        $uriString = $this->getURIIssue();
        if (empty($uriString)) {
            return null;
        }

        if (!is_null($this->m_section)) {
            $uriString .= '&'.CampRequest::SECTION_NR.'='.$this->m_section->number;
        } else {
            $uriString = null;
        }

        return $uriString;
    } // fn getURISection


    /**
     * Gets the article URI query path.
     * It fetches the article URL name from URI or current article list if any.
     *
     * @return string
     *      The article URI query path
     */
    private function getURIArticle()
    {
        $uriString = $this->getURISection();

        if (empty($uriString)) {
            return null;
        }

        if (!is_null($this->m_article)) {
            $uriString .= '&'.CampRequest::ARTICLE_NR.'='.$this->m_article->number;
        } else {
            $uriString = null;
        }

        return $uriString;
    } // fn getURISection


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
     * Returns true if the given parameter is restricted and can not 
     * be set from outside the URL object.
     *
     * @param string $p_parameterName
     * @return bool
     */
    public function isRestrictedParameter($p_parameterName)
    {
        return in_array($p_parameterName, CampURITemplatePath::$m_restriectedParameters);
    }
    
    
    /**
     * Parses the URI.
     * As URI was already parsed by CampURI, this function only takes care of
     * read and set the template name.
     *
     * @return void
     */
    private function parse()
    {
        $template = $this->readTemplate();
        if (!empty($template)) {
            $this->setTemplate($template);
        }
    } // fn parse


    /**
     * Sets the URL values.
     *
     * @return void
     */
    private function setURL()
    {
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

        $this->m_language = null;
        // sets the language identifier
        if ($this->getQueryVar(CampRequest::LANGUAGE_ID) > 0) {
            $this->m_language = new MetaLanguage($this->getQueryVar(CampRequest::LANGUAGE_ID));
        }
        if (is_null($this->m_language) || !$this->m_language->defined()) {
            $this->m_language = new MetaLanguage($this->m_publication->default_language->number);
        }
        if (is_null($this->m_language) || !$this->m_language->defined()) {
            CampTemplate::singleton()->trigger_error('not valid language');
            return;
        }

        $this->m_issue = null;
        // sets the issue number
        if ($this->getQueryVar(CampRequest::ISSUE_NR) > 0) {
            $this->m_issue = new MetaIssue($this->m_publication->identifier,
                                           $this->m_language->number,
                                           $this->getQueryVar(CampRequest::ISSUE_NR));
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
        // sets the section if any
        if ($this->getQueryVar(CampRequest::SECTION_NR) > 0) {
            $this->m_section = new MetaSection($this->m_publication->identifier,
                                               $this->m_issue->number,
                                               $this->m_language->number,
                                               $this->getQueryVar(CampRequest::SECTION_NR));
            if (is_null($this->m_section) || !$this->m_section->defined()) {
                CampTemplate::singleton()->trigger_error('not valid section');
                return;
            }
        }

        $this->m_article = null;
        // sets the article if any
        if ($this->getQueryVar(CampRequest::ARTICLE_NR) > 0) {
            $this->m_article = new MetaArticle($this->m_language->number,
                                               $this->getQueryVar(CampRequest::ARTICLE_NR));
            if (is_null($this->m_article) || !$this->m_article->defined()) {
                CampTemplate::singleton()->trigger_error('not valid article');
                return;
            }
        }

        $this->m_validURI = true;
        $this->validateCache(false);
    } // fn setURL


    /**
     * Sets the template name.
     *
     * @param string $p_value
     *      The template name
     *
     * @return void
     */
    private function setTemplate($p_value)
    {
        if ($this->isValidTemplate($p_value)) {
            $this->m_template = $p_value;
            $tplId = CampSystem::GetTemplateIdByName($p_value);
            $this->setQueryVar(CampRequest::TEMPLATE_ID, $tplId);
        }
    } // fn setTemplateName


    /**
     * Returns the template name from URI.
     *
     * @return null|string $template
     *      null on failure, otherwise the template name
     */
    private function readTemplate()
    {
        if ($this->getPath() == '' || $this->getPath() == '/') {
            return null;
        }

        $trimmedPath = trim($this->getPath(), '/');
        list($lookDir, $template) = explode('/', $trimmedPath);
        if ($lookDir != $this->m_templatesPrefix) {
            return null;
        }

        $validName = strpos($template, '.tpl');
        if (!$validName) {
            return null;
        }

        return $template;
    } // fn readTemplate


    /**
     * Returns whether the template name given is a valid template resource.
     *
     * @param string $p_templateName
     *      The name of the template from the URI path
     *
     * @return boolean
     *      true on success, false on failure
     */
    private function isValidTemplate($p_templateName)
    {
        $tplObj = new Template($p_templateName);
        if (is_object($tplObj) && $tplObj->exists() && $tplObj->fileExists()) {
            return true;
        }

        return false;
    } // fn isValidTemplate


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

        switch ($p_param) {
        case 'language':
            $this->m_uriQuery = $this->getURILanguage();
            break;
        case 'publication':
            $this->m_uriQuery = $this->getURIPublication();
            break;
        case 'issue':
            $this->m_uriQuery = $this->getURIIssue();
            break;
        case 'section':
            $this->m_uriQuery = $this->getURISection();
            break;
        case 'article':
            $this->m_uriQuery = $this->getURIArticle();
            break;
        case 'articleattachment':
            $context = CampTemplate::singleton()->context();
            $attachment = new Attachment($context->attachment->identifier);
            $this->m_uriPath = '/attachment/'.basename($attachment->getStorageLocation());
            break;
        default:
            if (empty($p_param)) {
                $this->m_uriQuery = $this->m_query;
            }
            break;
        }

        if ($p_param == 'publication' || $p_param == 'issue'
                || $p_param == 'section' || $p_param == 'article') {
            // gets the template name from the context
            $context = CampTemplate::singleton()->context();
            $template = $context->$p_param->template->name;

            if (empty($template)) {
                CampTemplate::singleton()->trigger_error('Invalid template');
                return;
            }

            $this->m_uriPath = '/' . $this->m_templatesPrefix . '/' . $template;
        }
        $this->validateCache(true);
    } // fn buildURI

} // class CampURITemplatePath

?>