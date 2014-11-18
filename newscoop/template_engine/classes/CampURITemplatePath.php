<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Publication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Issue.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Section.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Alias.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampURI.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampTemplate.php');

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
     * Parameters used for the language definition
     *
     * @var array
     */
    static private $m_languageParameters = array(
    CampRequest::LANGUAGE_ID
    );

    /**
     * Parameters used for the issue definition
     *
     * @var array
     */
    static private $m_issueParameters = array(
    CampRequest::LANGUAGE_ID,
    CampRequest::ISSUE_NR
    );

    /**
     * Parameters used for the section definition
     *
     * @var array
     */
    static private $m_sectionParameters = array(
    CampRequest::LANGUAGE_ID,
    CampRequest::ISSUE_NR,
    CampRequest::SECTION_NR
    );

    /**
     * Parameters used for the article definition
     *
     * @var array
     */
    static private $m_articleParameters = array(
    CampRequest::LANGUAGE_ID,
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
        $subdir = $this->m_config->getSetting('SUBDIR');
        $this->m_templatesPrefix = empty($subdir) ? 'tpl' : substr($subdir, 1) . '/tpl';
        $res = $this->setURL();
        if (PEAR::isError($res)) {
            $this->m_validURI = false;
            $this->m_errorCode = $res->getCode();
            if ($this->m_errorCode == self::INVALID_TEMPLATE
            && !is_null($this->m_publication)) {
            	$tplId = CampSystem::GetInvalidURLTemplate($this->m_publication->identifier, null, null, !$this->m_preview);
            	$template = new MetaTemplate($tplId);
            	if ($template->defined()) {
            		$this->m_template = $template;
            	}
            }
            CampTemplate::singleton()->trigger_error($res->getMessage());
        } else {
            foreach (CampURITemplatePath::$m_restrictedParameters as $parameter) {
                $this->setQueryVar($parameter);
            }
            $this->m_validURI = true;
        }
        $this->validateCache(false);
    } // fn __construct


    public function getQueryArray(array $p_keepParameters = array(),
    array $p_removeParameters = array()) {
        $queryArray = parent::getQueryArray();
        if (!is_null($this->m_language) && $this->m_language->defined()) {
            $queryArray[CampRequest::LANGUAGE_ID] = $this->m_language->number;
        }
        if (!is_null($this->m_issue) && $this->m_issue->defined()) {
            $queryArray[CampRequest::ISSUE_NR] = $this->m_issue->number;
        }
        if (!is_null($this->m_section) && $this->m_section->defined()) {
            $queryArray[CampRequest::SECTION_NR] = $this->m_section->number;
        }
        if (!is_null($this->m_article) && $this->m_article->defined()) {
            $queryArray[CampRequest::ARTICLE_NR] = $this->m_article->number;
        }
        if (count($p_removeParameters) > 0) {
            $removeKeys = array_combine($p_removeParameters,
            array_fill(0, count($p_removeParameters, null)));
            $queryArray = array_diff_key($queryArray, $removeKeys);
        }
        if (count($p_keepParameters)) {
            $keepKeys = array_combine($p_keepParameters,
            array_fill(0, count($p_keepParameters), null));
            $queryArray = array_intersect_key($queryArray, $keepKeys);
        }
        return $queryArray;
    }



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
     * Sets the URL values.
     *
     * @return PEAR_Error|null
     */
    private function setURL()
    {
        $this->setQueryVar('tpl', null);
        $this->setQueryVar('acid', null);

        $this->m_publication = null;
        $this->m_language = null;
        $this->m_issue = null;
        $this->m_section = null;
        $this->m_article = null;

        // gets the publication object based on site name (URI host)
        $alias = preg_replace('/^'.$this->getScheme().':\/\//', '', $this->getBase());
        $aliasObj = new Alias($alias);
        if ($aliasObj->exists()) {
            $this->m_publication = new MetaPublication($aliasObj->getPublicationId());
        }
        if (is_null($this->m_publication) || !$this->m_publication->defined()) {
            return new PEAR_Error("Invalid site name '$alias' in URL.", self::INVALID_SITE_NAME);
        }

        // sets the language identifier
        if (CampRequest::GetVar(CampRequest::LANGUAGE_ID) > 0) {
            $this->m_language = new MetaLanguage(CampRequest::GetVar(CampRequest::LANGUAGE_ID));
        } else {
            $this->m_language = new MetaLanguage($this->m_publication->default_language->number);
        }
        if (!$this->m_language->defined()) {
            return new PEAR_Error("Invalid language identifier in URL.", self::INVALID_LANGUAGE);
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
            return new PEAR_Error("Invalid issue identifier in URL.", self::INVALID_ISSUE);
        }

        // sets the section if any
        if (CampRequest::GetVar(CampRequest::SECTION_NR) > 0) {
            $this->m_section = new MetaSection($this->m_publication->identifier,
            $this->m_issue->number, $this->m_language->number,
            CampRequest::GetVar(CampRequest::SECTION_NR));
            if (!$this->m_section->defined()) {
                return new PEAR_Error("Invalid section identifier in URL.", self::INVALID_SECTION);
            }
        }

        // sets the article if any
        if (CampRequest::GetVar(CampRequest::ARTICLE_NR) > 0) {
            $this->m_article = new MetaArticle($this->m_language->number,
            CampRequest::GetVar(CampRequest::ARTICLE_NR));
            if (!$this->m_article->defined()) {
                return new PEAR_Error("Invalid article identifier in URL.", self::INVALID_ARTICLE);
            }
        }

        $this->m_template = new MetaTemplate($this->getTemplate($this->readTemplate()), $this->getThemePath());
        if (!$this->m_template->defined()) {
            return new PEAR_Error("Invalid template in URL or no default template specified.",
            self::INVALID_TEMPLATE);
        }

        $this->m_validURI = true;
        $this->validateCache(false);
    } // fn setURL


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

        $template = str_replace($this->m_templatesPrefix . '/', '', trim($this->getPath(), '/'));
        $validName = strpos($template, '.tpl');
        if (!$validName) {
            return null;
        }

        return $template;
    } // fn readTemplate


    /**
     * Sets the URI path and query values based on given parameters.
     *
     * @param array $p_params
     *      An array of valid URL parameters
     * @param boolean $p_preview
     *      If true, will keep the preview parameters in the URL
     *
     * @return void
     */
    protected function buildURI(array &$p_params = array(), $p_preview = false)
    {
        if ($this->isValidCache()) {
            return;
        }

        $parameter = count($p_params) > 0 ? strtolower(array_shift($p_params)) : null;

        switch ($parameter) {
            case 'language':
                $this->m_buildPath = $this->buildPath(CampSystem::GetTemplate($this->m_language->number,
                $this->m_publication->identifier));
                $keepParams = CampURITemplatePath::$m_languageParameters;
                if ($p_preview) {
                    $keepParams = array_merge(CampURI::$m_previewParameters, $keepParams);
                }
                $this->m_buildQueryArray = $this->getQueryArray($keepParams);
                $p_params = array();
                break;
            case 'publication':
                $this->m_buildPath = $this->buildPath(CampSystem::GetTemplate($this->m_language->number,
                $this->m_publication->identifier));
                $keepParams = CampURITemplatePath::$m_languageParameters;
                if ($p_preview) {
                    $keepParams = array_merge(CampURI::$m_previewParameters, $keepParams);
                }
                $this->m_buildQueryArray = $this->getQueryArray($keepParams);
                $p_params = array();
                break;
            case 'issue':
                $this->m_buildPath = $this->buildPath(CampSystem::GetIssueTemplate($this->m_language->number,
                $this->m_publication->identifier, $this->m_issue->number));
                $keepParams = CampURITemplatePath::$m_issueParameters;
                if ($p_preview) {
                    $keepParams = array_merge(CampURI::$m_previewParameters, $keepParams);
                }
                $this->m_buildQueryArray = $this->getQueryArray($keepParams);
                $p_params = array();
                break;
            case 'section':
                $this->m_buildPath = $this->buildPath(CampSystem::GetSectionTemplate($this->m_language->number,
                $this->m_publication->identifier, $this->m_issue->number, $this->m_section->number));
                $keepParams = CampURITemplatePath::$m_sectionParameters;
                if ($p_preview) {
                    $keepParams = array_merge(CampURI::$m_previewParameters, $keepParams);
                }
                $this->m_buildQueryArray = $this->getQueryArray($keepParams);
                $p_params = array();
                break;
            case 'article':
                $this->m_buildPath = $this->buildPath(CampSystem::GetArticleTemplate($this->m_language->number,
                $this->m_publication->identifier, $this->m_issue->number, $this->m_section->number));
                $keepParams = CampURITemplatePath::$m_articleParameters;
                if ($p_preview) {
                    $keepParams = array_merge(CampURI::$m_previewParameters, $keepParams);
                }
                $this->m_buildQueryArray = $this->getQueryArray($keepParams);
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
                    parent::buildURI($p_params, $p_preview);
                    if (count($p_params) == $count) {
                        array_shift($p_params);
                    }
                }
        }

        if (count($p_params) > 0) {
            $this->buildURI($p_params);
        }

        if (is_null($this->m_buildPath)) {
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