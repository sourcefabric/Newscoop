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

        try {
            $this->setURLType(URLTYPE_SHORT_NAMES);
            // HUGE TODO: rewrite this - remove globals controller usage!
            if (array_key_exists('controller', $GLOBALS)) {
                if (is_object($GLOBALS['controller'])) {
                    $this->setURL($GLOBALS['controller']->getRequest());
                } else {
                    $this->setURLFromSymfony(Zend_Registry::get('container')->getService('request'));
                }
            } else {
                $this->setURL(new Zend_Controller_Request_Http());
            }
            
            $this->m_validURI = true;
            $this->validateCache(false);
        } catch (Exception $e) {
            $this->m_validURI = false;
            $this->m_errorCode = $e->getCode();

            if (!is_null($this->m_publication)) {
                $tplId = CampSystem::GetInvalidURLTemplate($this->m_publication->identifier, null, null, !$this->m_preview);
                $themePath = $this->getThemePath();
                $tplId = substr($tplId, strlen($themePath));
                $template = new MetaTemplate($tplId, $themePath);
                if ($template->defined()) {
                    $this->m_template = $template;
                }
            }

            CampTemplate::singleton()->trigger_error($e->getMessage());
        }
    }

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
     * Get publication by site name
     *
     * @return MetaPublication
     */
    private function _getPublication()
    {
        $alias = preg_replace('/^' . $this->getScheme() . ':\/\//', '', $this->getBase());
        $aliasObj = new Alias($alias);

        if ($aliasObj->exists()) {
            $publication = new MetaPublication($aliasObj->getPublicationId());
        }

        if (empty($publication) || !$publication->defined()) {
            throw new InvalidArgumentException("Invalid site name '$alias' in URL.", self::INVALID_SITE_NAME);
        }

        return $publication;
    }

    /**
     * Get language by code
     *
     * @param string $code
     * @param MetaPublication $publication
     * @return MetaLanguage
     */
    private function _getLanguage($code, MetaPublication $publication)
    {
        $language = $publication->default_language;
        
        if (!empty($code)) {
            $langArray = Language::GetLanguages(null, $code);
            if (is_array($langArray) && sizeof($langArray) == 1) {
                $language = new MetaLanguage($langArray[0]->getLanguageId());
            }
        }

        if (!$language->defined()) {
            throw new InvalidArgumentException("Invalid language identifier in URL.", self::INVALID_LANGUAGE);
        }

        return $language;
    }

    /**
     * Get issue
     *
     * @param string $name
     * @return MetaIssue
     */
    private function _getIssue($name, MetaLanguage $language, MetaPublication $publication)
    {
        if (!empty($name)) {
            $issueArray = Issue::GetIssues($publication->identifier, $language->number, null, $name, null, !$this->m_preview);
            if (is_array($issueArray) && sizeof($issueArray) == 1) {
                $issue = new MetaIssue($publication->identifier, $language->number, $issueArray[0]->getIssueNumber());
            } else {
                throw new InvalidArgumentException("Invalid issue identifier in URL.", self::INVALID_ISSUE);
            }
        } else {
            $issueObj = Issue::GetCurrentIssue($publication->identifier, $language->number);
            $issue = new MetaIssue($publication->identifier, $language->number, $issueObj->getIssueNumber());
            if (!$issue->defined()) {
                throw new InvalidArgumentException("No published issue was found.", self::INVALID_ISSUE);
            }
        }

        return $issue;
    }

    /**
     * Get section
     *
     * @param string $name
     * @param MetaIssue $issue
     * @param MetaLanguage $language
     * @param MetaPublication $publication
     * @return MetaSection
     */
    private function _getSection($name, MetaIssue $issue, MetaLanguage $language, MetaPublication $publication)
    {
        if (empty($name)) {
            return null;
        }

        $sections = Section::GetSections($publication->identifier, $issue->number, $language->number, $name);
        if (is_array($sections) && sizeof($sections) == 1) {
            return new MetaSection($publication->identifier, $issue->number, $language->number, $sections[0]->getSectionNumber());
        }

        throw new InvalidArgumentException("Invalid section identifier in URL.", self::INVALID_SECTION);
    }

    /**
     * Get article
     *
     * @param int $articleNo
     * @param MetaLanguage $language
     * @return MetaArticle
     */
    private function _getArticle($articleNo, MetaLanguage $language)
    {
        if (empty($articleNo)) {
            return null;
        }

        $articleObj = new Article($language->number, $articleNo);
        if (!$articleObj->exists() || (!$this->m_preview && !$articleObj->isPublished())) {
            throw new InvalidArgumentException("Invalid article identifier in URL.", self::INVALID_ARTICLE);
        }

        return new MetaArticle($language->number, $articleObj->getArticleNumber());
    }

    /**
     * Get template
     *
     * @return MetaTemplate
     */
    private function _getTemplate()
    {
        $templateId = CampRequest::GetVar(CampRequest::TEMPLATE_ID);
        $themePath = $this->m_issue->defined() ? $this->m_issue->theme_path : $this->m_publication->theme_path;
        $template = new MetaTemplate(parent::getTemplate($templateId), $themePath);
        if (!$template->defined()) {
            throw new InvalidArgumentException("Invalid template in URL or no default template specified.", self::INVALID_TEMPLATE);
        }

        CampTemplate::singleton()->config_dir = APPLICATION_PATH . '/../themes/' . $themePath . '_conf';

        return $template;
    }

    /**
     * Sets the URL values
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return void|PEAR_Error
     */
    private function setURL(Zend_Controller_Request_Abstract $request)
    {
        $this->setQueryVar('acid', null);
        $this->m_publication = $this->_getPublication();
        $controller = $request->getParam('controller');
        if ($controller != 'index') {
            $language = $controller;
        } else {
            $language = $request->getParam('language');
        }
        if ($request->getParam('webcode')) {
            if (!empty($language)) {
                $webcodeLanguageId = Language::GetLanguageIdByCode($language);
            } else {
                $webcodeLanguageId = $this->m_publication->default_language->number;
            }

            $webcode = trim(trim($request->getParam('webcode'), '@+'));
            $article = Zend_Registry::get('container')->getService('webcode')->findArticleByWebcode($webcode);
            if ($article) {
                $article_no = $article->getNumber();
                $webcodeLanguageId = $article->getLanguageId();
            }

            $metaArticle = new MetaArticle($webcodeLanguageId, $article_no);
            $this->m_article = $metaArticle;
            if ($metaArticle->defined()) {
                $this->m_language = $this->m_article->language;
                $this->m_publication = $this->m_article->publication;
            } else {
                $this->m_language = $this->_getLanguage($metaArticle->language, $this->m_publication);
            }
            $this->m_issue = $this->m_article->issue;
            $this->m_section = $this->m_article->section;
        } else {
            $this->m_language = $this->_getLanguage($language, $this->m_publication);
            $this->m_issue = $this->_getIssue($request->getParam('issue'), $this->m_language, $this->m_publication);
            $this->m_section = $this->_getSection($request->getParam('section'), $this->m_issue, $this->m_language, $this->m_publication);
            $this->m_article = $this->_getArticle($request->getParam('articleNo'), $this->m_language);
        }
        $this->m_template = $this->_getTemplate();

    }

    /**
     * Sets the URL values
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    private function setURLFromSymfony($request)
    {
        $this->setQueryVar('acid', null);
        $this->m_publication = $this->_getPublication();
        $language = $request->get('language', 'en');

        $this->m_language = $this->_getLanguage($language, $this->m_publication);
        $this->m_issue = $this->_getIssue($request->get('issue'), $this->m_language, $this->m_publication);
        $this->m_section = $this->_getSection($request->get('section'), $this->m_issue, $this->m_language, $this->m_publication);
        $this->m_article = $this->_getArticle($request->get('articleNo'), $this->m_language);

        $this->m_template = $this->_getTemplate();
    }

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
            case 'id':
                $option = isset($p_params[0]) ? array_shift($p_params) : null;
                if (is_null($option)) {
                    break;
                }
                if (is_null($this->_themePath)) {
                    $this->_themePath = CampSystem::GetThemePath($this->m_language->number,
                                    $this->m_publication->identifier,
                                    $this->m_issue->number);
                }
                $pathRsc = new Resource();
                $pathRsc->setName('buildPage');
                $pathRsc->setPath($this->_themePath.$option);
                $resourceId = new ResourceId('template_engine/classes/CampURIShortNames');
                $pathRsc = $resourceId->getService(ISyncResourceService::NAME)->getSynchronized($pathRsc);
                if (!is_null($pathRsc) && $pathRsc->exists()) {
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
}
