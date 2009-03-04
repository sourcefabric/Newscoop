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
    static private $m_restrictedParameters = array(
    CampRequest::LANGUAGE_ID,
    CampRequest::PUBLICATION_ID,
    CampRequest::ISSUE_NR,
    CampRequest::SECTION_NR,
    CampRequest::ARTICLE_NR
    );

    /**
     * Templates directory
     *
     * @var string
     */
    private $m_templatesPrefix = null;

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
        $this->setURL();
        $this->parse();
        foreach (CampURITemplatePath::$m_restrictedParameters as $parameter) {
            $this->setQueryVar($parameter);
        }
    } // fn __construct


    public function getQueryArray() {
        $queryArray = parent::getQueryArray();
        if ($this->m_language->defined()) {
            $queryArray[CampRequest::LANGUAGE_ID] = $this->m_language->number;
        }
        if ($this->m_issue->defined()) {
            $queryArray[CampRequest::ISSUE_NR] = $this->m_issue->number;
        }
        if ($this->m_section->defined()) {
            $queryArray[CampRequest::SECTION_NR] = $this->m_section->number;
        }
        if ($this->m_article->defined()) {
            $queryArray[CampRequest::ARTICLE_NR] = $this->m_article->number;
        }
        return $queryArray;
    }


    /**
     * Gets the current template name.
     *
     * @return string
     *      The name of the template
     */
    public function getTemplate()
    {
        if (!is_null($this->m_template)) {
            return $this->m_template->name;
        }

        $template = $this->readTemplate();
        if (is_null($template)) {
            $template = CampSystem::GetTemplate($this->language->number,
            $this->publication->identifier, $this->issue->number,
            $this->section->number, $this->article->number);
        }
        $this->setTemplate($template);

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
        if ($this->m_language->defined()) {
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

        if ($this->m_issue->defined()) {
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

        if ($this->m_section->defined()) {
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

        if ($this->m_article->defined()) {
            $uriString .= '&'.CampRequest::ARTICLE_NR.'='.$this->m_article->number;
        } else {
            $uriString = null;
        }

        return $uriString;
    } // fn getURISection


    /**
     * @return array
     *      An array containing all the form parameters to print out
     */
    public function getFormParameters()
    {
        $parameters = array();
        $queryParameters = $this->getQueryArray();
        foreach ($queryParameters as $paramName => $paramValue) {
            $parameters[$i++] = array('name' => $paramName, 'value' => $paramValue);
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
        return in_array($p_parameterName, CampURITemplatePath::$m_restrictedParameters);
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
        $this->getTemplate();
    } // fn parse


    /**
     * Sets the URL values.
     *
     * @return void
     */
    private function setURL()
    {
        $this->setQueryVar('tpl', null);
        $this->setQueryVar('acid', null);

        $this->m_publication = new MetaPublication();
        $this->m_language = new MetaLanguage();
        $this->m_issue = new MetaIssue();
        $this->m_section = new MetaSection();
        $this->m_article = new MetaArticle();

        // gets the publication object based on site name (URI host)
        $alias = ltrim($this->getBase(), $this->getScheme().'://');
        $aliasArray = Alias::GetAliases(null, null, $alias);
        if (is_array($aliasArray) && sizeof($aliasArray) == 1) {
            $this->m_publication = new MetaPublication($aliasArray[0]->getPublicationId());
        }
        if (!$this->m_publication->defined()) {
            CampTemplate::singleton()->trigger_error('Invalid site alias in URL.');
            return;
        }

        // sets the language identifier
        if (CampRequest::GetVar(CampRequest::LANGUAGE_ID) > 0) {
            $this->m_language = new MetaLanguage(CampRequest::GetVar(CampRequest::LANGUAGE_ID));
        }
        if (!$this->m_language->defined()) {
            $this->m_language = new MetaLanguage($this->m_publication->default_language->number);
        }
        if (!$this->m_language->defined()) {
            CampTemplate::singleton()->trigger_error('Invalid language number in URL.');
            return;
        }

        // sets the issue number
        if (CampRequest::GetVar(CampRequest::ISSUE_NR) > 0) {
            $this->m_issue = new MetaIssue($this->m_publication->identifier,
            $this->m_language->number, CampRequest::GetVar(CampRequest::ISSUE_NR));
        } else {
            $issueObj = Issue::GetCurrentIssue($this->m_publication->identifier,
            $this->m_language->number);
            $this->m_issue = new MetaIssue($this->m_publication->identifier,
            $this->m_language->number, $issueObj->getIssueNumber());
        }
        if (!$this->m_issue->defined()) {
            CampTemplate::singleton()->trigger_error('Invalid issue number in URL.');
            return;
        }

        // sets the section if any
        if (CampRequest::GetVar(CampRequest::SECTION_NR) > 0) {
            $this->m_section = new MetaSection($this->m_publication->identifier,
            $this->m_issue->number, $this->m_language->number,
            CampRequest::GetVar(CampRequest::SECTION_NR));
            if (!$this->m_section->defined()) {
                CampTemplate::singleton()->trigger_error('Invalid section number in URL.');
                return;
            }
        }

        // sets the article if any
        if (CampRequest::GetVar(CampRequest::ARTICLE_NR) > 0) {
            $this->m_article = new MetaArticle($this->m_language->number,
            CampRequest::GetVar(CampRequest::ARTICLE_NR));
            if (!$this->m_article->defined()) {
                CampTemplate::singleton()->trigger_error('Invalid article number in URL.');
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
            $this->m_template = new MetaTemplate($p_value);
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
        $pathParts = explode('/', $trimmedPath);
        $tplDir = array_shift($pathParts);
        $template = implode('/', $pathParts);
        if ($tplDir != $this->m_templatesPrefix) {
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
     * @param array $p_params
     *      An array of valid URL parameters
     *
     * @return void
     */
    protected function buildURI(array &$p_params = array())
    {
        if ($this->isValidCache()) {
            return;
        }

        $parameter = count($p_params) > 0 ? strtolower(array_shift($p_params)) : null;

        switch ($parameter) {
            case 'language':
                $this->m_buildQuery = $this->getURILanguage();
                $this->m_buildPath = $this->buildPath(CampSystem::GetTemplate($this->m_language->number,
                $this->m_publication->identifier));
                $p_params = array();
                break;
            case 'publication':
                $this->m_buildQuery = $this->getURIPublication();
                $this->m_buildPath = $this->buildPath(CampSystem::GetTemplate($this->m_language->number,
                $this->m_publication->identifier));
                $p_params = array();
                break;
            case 'issue':
                $this->m_buildQuery = $this->getURIIssue();
                $this->m_buildPath = $this->buildPath(CampSystem::GetIssueTemplate($this->m_language->number,
                $this->m_publication->identifier, $this->m_issue->number));
                $p_params = array();
                break;
            case 'section':
                $this->m_buildQuery = $this->getURISection();
                $this->m_buildPath = $this->buildPath(CampSystem::GetSectionTemplate($this->m_language->number,
                $this->m_publication->identifier, $this->m_issue->number, $this->m_section->number));
                $p_params = array();
                break;
            case 'article':
                $this->m_buildQuery = $this->getURIArticle();
                $this->m_buildPath = $this->buildPath(CampSystem::GetArticleTemplate($this->m_language->number,
                $this->m_publication->identifier, $this->m_issue->number, $this->m_section->number));
                $p_params = array();
                break;
            case 'template':
                $option = isset($p_params[0]) ? array_shift($p_params) : null;
                if (!is_null($option) && $this->isValidTemplate($option)) {
                    $this->m_buildPath = $this->buildPath($option);
                }
                break;
            default:
                if (!empty($parameter)) {
                    array_unshift($p_params, $parameter);
                    $count = count($p_params);
                    parent::buildURI($p_params);
                    if (count($p_params) == $count) {
                        array_shift($p_params);
                    }
                }
        }

        if (count($p_params) > 0) {
            $this->buildURI($p_params);
        }

        if (is_null($this->m_buildPath)) {
//            $template = CampSystem::GetTemplate($this->m_language->number,
//            $this->m_publication->identifier, $this->m_issue->number,
//            $this->m_section->number, $this->m_article->number);

        	$template = $this->getTemplate();
            if (empty($template)) {
                CampTemplate::singleton()->trigger_error('Invalid template in context');
                return;
            }
            $this->m_buildPath = $this->buildPath($template);
        }

        if (is_null($this->m_buildQuery)) {
            $this->m_buildQuery = CampURI::QueryArrayToString($this->m_buildQueryArray);
        }

        $this->validateCache(true);
    } // fn buildURI


    private function buildPath($p_template) {
        if (!empty($p_template)) {
            return '/'.$this->m_templatesPrefix.'/'.$p_template;
        }
        return '/';
    }

} // class CampURITemplatePath

?>