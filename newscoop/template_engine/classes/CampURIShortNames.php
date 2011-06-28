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
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\ISyncResourceService;
use Newscoop\Entity\Resource;

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'] . '/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Publication.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Issue.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Section.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'] . '/classes/Alias.php');
require_once($GLOBALS['g_campsiteDir'] . '/template_engine/classes/CampRequest.php');
require_once($GLOBALS['g_campsiteDir'] . '/template_engine/classes/CampURI.php');
require_once($GLOBALS['g_campsiteDir'] . '/template_engine/classes/CampTemplate.php');

/**
 * Class CampURIShortNames
 */
class CampURIShortNames extends CampURI
{

    /**
     * The theme path storage
     */
    protected $_themePath = null;

    /**
     * Parameters that are restricted to CampURIShortNames object use.
     *
     * @var array
     */
    static private $m_restrictedParameters = array(
    );

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
        $res = $this->setURL();
        if (PEAR::isError($res)) {
            $this->m_validURI = false;
            $this->m_errorCode = $res->getCode();
            if (!is_null($this->m_publication)) {
                $tplId = CampSystem::GetInvalidURLTemplate($this->m_publication->identifier);
                $template = new MetaTemplate($tplId);
                if ($template->defined()) {
                    $this->m_template = $template;
                }
            }
            CampTemplate::singleton()->trigger_error($res->getMessage());
        } else {
            $this->m_validURI = true;
        }
        $this->validateCache(false);
    }

// fn __construct

    /**
     * Gets the language URI path.
     *
     * @return string
     *      The language URI path
     */
    private function getURILanguage()
    {
        $uriString = null;
        if (!is_null($this->m_language) && $this->m_language->defined()) {
            $uriString = $this->m_config->getSetting('SUBDIR') . '/' . $this->m_language->code . '/';
        }

        return $uriString;
    }

// fn getURILanguage

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

        if (!is_null($this->m_issue) && $this->m_issue->defined()) {
            $uriString .= $this->m_issue->url_name . '/';
        } else {
            $uriString = null;
        }

        return $uriString;
    }

// fn getURIIssue

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

        if (!is_null($this->m_section) && $this->m_section->defined()) {
            $uriString .= $this->m_section->url_name . '/';
        } else {
            $uriString = null;
        }

        return $uriString;
    }

// fn getURISection

    /**
     * Gets the article URI path.
     * It fetches the article URL name from URI or current article list if any.
     *
     * @return string
     *      The article URI path
     */
    private function getURIArticle()
    {
        if (!is_null($this->m_article) && $this->m_article->defined()) {
            $uriString = $this->getURISection();
            $uriString .= $this->m_article->url_name . '/';
            if ($seo = $this->m_publication->seo) {
                $article = $this->m_article->seo_url_end;
                $uriString .= $article;
            }
        } else {
            $uriString = null;
        }

        return $uriString;
    }

// fn getURIArticle

    /**
     * @return array
     *      An array containing all the form parameters to print out
     */
    public function getFormParameters()
    {
        $baseParameters = array('IdLanguage', 'IdPublication',
            'NrIssue', 'NrSection', 'NrArticle');
        $parameters = array();

        $queryParameters = $this->getQueryArray();
        foreach ($queryParameters as $paramName => $paramValue) {
            if (in_array($paramName, $baseParameters)) {
                continue;
            }
            $parameters[] = array('name' => $paramName, 'value' => $paramValue);
        }

        return $parameters;
    }

// fn getFormParameters

    /**
     * Returns true if the given parameter is restricted and can not
     * be set from outside the URL object.
     *
     * @param string $p_parameterName
     * @return bool
     */
    public function isRestrictedParameter($p_parameterName)
    {
        return in_array($p_parameterName,
                CampURIShortNames::$m_restrictedParameters);
    }

    /**
     * Sets the URL values.
     *
     * Algorithm:
     * - identify object (e.g.: publication, language, issue, section, article)
     *     - object defined
     *         - valid object?
     *             - yes: set
     *             - no: return error
     *     - object undefined
     *         - has default value?
     *             - yes: set
     *             - no:
     *                 - object mandatory?
     *                     - yes: return error
     *                     - no: continue
     *
     * @return PEAR_Error
     *
     */
    private function setURL()
    {
        $this->setQueryVar('acid', null);

        $this->m_publication = null;
        $this->m_language = null;
        $this->m_issue = null;
        $this->m_section = null;
        $this->m_article = null;

        // gets the publication object based on site name (URI host)
        $alias = preg_replace('/^' . $this->getScheme() . ':\/\//', '',
                $this->getBase());
        $aliasObj = new Alias($alias);
        if ($aliasObj->exists()) {
            $this->m_publication = new MetaPublication($aliasObj->getPublicationId());
        }
        if (is_null($this->m_publication) || !$this->m_publication->defined()) {
            return new PEAR_Error("Invalid site name '$alias' in URL.", self::INVALID_SITE_NAME);
        }

        // reads parameters values if any
        $params = str_replace($this->m_config->getSetting('SUBDIR'), '',
                $this->getPath());
        $cParams = explode('/', trim($params, '/'));
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
        if ($cParamsSize >= 4) {
            $cArticleSName = $cParams[3];
        }

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
            return new PEAR_Error("Invalid language identifier in URL.", self::INVALID_LANGUAGE);
        }

        // gets the issue number and sets the issue short name
        if (!empty($cIssueSName)) {
            $publishedOnly = !$this->m_preview;
            $issueArray = Issue::GetIssues($this->m_publication->identifier,
                            $this->m_language->number, null, $cIssueSName, null,
                            $publishedOnly);
            if (is_array($issueArray) && sizeof($issueArray) == 1) {
                $this->m_issue = new MetaIssue($this->m_publication->identifier,
                                $this->m_language->number,
                                $issueArray[0]->getIssueNumber());
            } else {
                return new PEAR_Error("Invalid issue identifier in URL.", self::INVALID_ISSUE);
            }
        } else {
            $issueObj = Issue::GetCurrentIssue($this->m_publication->identifier,
                            $this->m_language->number);
            $this->m_issue = new MetaIssue($this->m_publication->identifier,
                            $this->m_language->number, $issueObj->getIssueNumber());
            if (!$this->m_issue->defined()) {
                return new PEAR_Error("No published issue was found.", self::INVALID_ISSUE);
            }
        }

        // gets the section number and sets the section short name
        if (!empty($cSectionSName)) {
            $sectionArray = Section::GetSections($this->m_publication->identifier,
                            $this->m_issue->number, $this->m_language->number,
                            $cSectionSName);
            if (is_array($sectionArray) && sizeof($sectionArray) == 1) {
                $this->m_section = new MetaSection($this->m_publication->identifier,
                                $this->m_issue->number,
                                $this->m_language->number,
                                $sectionArray[0]->getSectionNumber());
            } else {
                return new PEAR_Error("Invalid section identifier in URL.", self::INVALID_SECTION);
            }
        }

        // gets the article number and sets the article short name
        if (!empty($cArticleSName)) {
            // we pass article short name as article identifier as they are
            // the same for Campsite, we will have to change this in the future
            $articleObj = new Article($this->m_language->number, $cArticleSName);
            if (!$articleObj->exists() || (!$this->m_preview && !$articleObj->isPublished())) {
                return new PEAR_Error("Invalid article identifier in URL.", self::INVALID_ARTICLE);
            }
            $this->m_article = new MetaArticle($this->m_language->number,
                            $articleObj->getArticleNumber());
        }
        $templateId = CampRequest::GetVar(CampRequest::TEMPLATE_ID);
        $this->m_template = new MetaTemplate($this->getTemplate($templateId));
        if (!$this->m_template->defined()) {
            return new PEAR_Error("Invalid template in URL or no default template specified.",
                    self::INVALID_TEMPLATE);
        }

        $this->m_validURI = true;
        $this->validateCache(false);
    }

// fn setURL

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
            case 'publication':
                $this->m_buildPath = $this->getURILanguage();
                if ($p_preview) {
                    $this->m_buildQueryArray = $this->getQueryArray(CampURI::$m_previewParameters);
                } else {
                    $this->m_buildQueryArray = array();
                }
                $p_params = array();
                break;
            case 'issue':
                $this->m_buildPath = $this->getURIIssue();
                if ($p_preview) {
                    $this->m_buildQueryArray = $this->getQueryArray(CampURI::$m_previewParameters);
                } else {
                    $this->m_buildQueryArray = array();
                }
                $p_params = array();
                break;
            case 'section':
                $this->m_buildPath = $this->getURISection();
                if ($p_preview) {
                    $this->m_buildQueryArray = $this->getQueryArray(CampURI::$m_previewParameters);
                } else {
                    $this->m_buildQueryArray = array();
                }
                $p_params = array();
                break;
            case 'article':
                $this->m_buildPath = $this->getURIArticle();
                if ($p_preview) {
                    $this->m_buildQueryArray = $this->getQueryArray(CampURI::$m_previewParameters);
                } else {
                    $this->m_buildQueryArray = array();
                }
                $p_params = array();
                break;
            case 'template':
                $option = isset($p_params[0]) ? array_shift($p_params) : null;
                if (is_null($this->_themePath)) {
                    $this->_themePath = CampSystem::GetThemePath($this->m_language->number,
                                    $this->m_publication->identifier,
                                    $this->m_issue->number);
                }
                $pathRsc = new Resource();
                $pathRsc->setName('buildPage');
                $pathRsc->setPath($this->_themePath.$option);
                $resourceId = new ResourceId('template_engine/classes/CampURIShortNames');
                $pathRsc = $syncResourceService = $resourceId->getService(ISyncResourceService::NAME)->getSynchronized($pathRsc);
                if (!is_null($option) && !is_null($pathRsc) && $pathRsc->exists()) {
                    $this->m_buildQueryArray[CampRequest::TEMPLATE_ID] = $pathRsc->getId();
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

        if (!is_null($this->m_language) && $this->m_language->defined() && is_null($this->m_buildPath)) {
            $this->m_buildPath = $this->m_config->getSetting('SUBDIR') . '/' . $this->m_language->code . '/';
            if (!is_null($this->m_issue) && $this->m_issue->defined()) {
                $this->m_buildPath .= $this->m_issue->url_name . '/';
                if (!is_null($this->m_section) && $this->m_section->defined()) {
                    $this->m_buildPath .= $this->m_section->url_name . '/';
                    if (!is_null($this->m_article) && $this->m_article->defined()) {
                        $this->m_buildPath = $this->getURIArticle();
                    }
                }
            }
        }


        if (is_null($this->m_buildQuery)) {
            $this->m_buildQuery = CampURI::QueryArrayToString($this->m_buildQueryArray);
        }
        $this->validateCache(true);
    }

// fn buildURI
}

// class CampURIShortNames
?>