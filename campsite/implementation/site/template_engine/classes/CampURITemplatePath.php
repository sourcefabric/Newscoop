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
     * Holds the CampURITemplatePath object
     *
     * @var object
     */
    private static $m_instance = null;

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
    private $m_lookDir = null;

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
    protected function __construct($p_uri = null)
    {
        parent::__construct($p_uri);
        $this->setURLType(URLTYPE_TEMPLATE_PATH);
        $this->m_lookDir = 'look';
        $this->parse();
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
            self::$m_instance = new CampURITemplatePath($p_uri);
        }

        return self::$m_instance;
    } // fn singleton


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

        $template = CampSystem::GetTemplate($this->getQueryVar(CampRequest::LANGUAGE_ID),
                                            $this->getQueryVar(CampRequest::PUBLICATION_ID),
                                            $this->getQueryVar(CampRequest::ISSUE_NR),
                                            $this->getQueryVar(CampRequest::SECTION_NR),
                                            $this->getQueryVar(CampRequest::ARTICLE_NR));
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
        $context = CampTemplate::singleton()->context();
        if (is_object($context->language) && $context->language->defined) {
            $uriString = CampRequest::LANGUAGE_ID.'='.$context->language->number;
        } else {
            $languageId = $this->getQueryVar(CampRequest::LANGUAGE_ID);
            if (empty($languageId)) {
                return null;
            }
            $uriString = CampRequest::LANGUAGE_ID.'='.$languageId;
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

        $context = CampTemplate::singleton()->context();
        if (is_object($context->publication) && $context->publication->defined) {
            $uriString .= '&'.CampRequest::PUBLICATION_ID.'='.$context->publication->identifier;
        } else {
            $publicationId = $this->getQueryVar(CampRequest::PUBLICATION_ID);
            if (empty($publicationId)) {
                return null;
            }
            $uriString .= '&'.CampRequest::PUBLICATION_ID.'='.$publicationId;
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

        $context = CampTemplate::singleton()->context();
        if (is_object($context->issue) && $context->issue->defined) {
            $uriString .= '&'.CampRequest::ISSUE_NR.'='.$context->issue->number;
        } else {
            $issueNr = $this->getQueryVar(CampRequest::ISSUE_NR);
            if (empty($issueNr)) {
                return null;
            }
            $uriString .= '&'.CampRequest::ISSUE_NR.'='.$issueNr;
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

        $context = CampTemplate::singleton()->context();
        if (is_object($context->section) && $context->section->defined) {
            $uriString .= '&'.CampRequest::SECTION_NR.'='.$context->section->number;
        } else {
            $sectionNr = $this->getQueryVar(CampRequest::SECTION_NR);
            if (empty($sectionNr)) {
                return null;
            }
            $uriString .= '&'.CampRequest::SECTION_NR.'='.$sectionNr;
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

        $context = CampTemplate::singleton()->context();
        if (is_object($context->article) && $context->article->defined) {
            $uriString .= '&'.CampRequest::ARTICLE_NR.'='.$context->article->number;
        } else {
            $articleNr = $this->getQueryVar(CampRequest::ARTICLE_NR);
            if (empty($articleNr)) {
                return null;
            }
            $uriString .= '&'.CampRequest::ARTICLE_NR.'='.$articleNr;
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
        // gets the publication object based on site name (URI host)
        $alias = ltrim($this->getBase(), $this->getScheme().'://');
        $aliasArray = Alias::GetAliases(null, null, $alias);
        if (is_array($aliasArray) && sizeof($aliasArray) == 1) {
            $aliasObj = $aliasArray[0];
            $cPubId = $aliasObj->getPublicationId();
            $pubObj = new Publication($cPubId);
            if (!is_object($pubObj) || !$pubObj->exists()) {
                $cPubId = 0;
                $pubObj = null;
            }
            $this->setQueryVar(CampRequest::PUBLICATION_ID, $cPubId);
        }

        if (empty($cPubId)) {
            CampTemplate::singleton()->trigger_error('not valid site alias');
            return;
        }

        // no path means we are at the home page
        if ($this->getPath() == '' || $this->getPath() == '/') {
            // sets the language identifier if necessary
            if ($this->getQueryVar(CampRequest::LANGUAGE_ID) == 0) {
                $cLangId = $pubObj->getLanguageId();
                $this->setQueryVar(CampRequest::LANGUAGE_ID, $cLangId);
            }
            // sets the issue number if necessary
            if ($this->getQueryVar(CampRequest::ISSUE_NR) == 0) {
                $query = 'SELECT MAX(Number) FROM Issues '
                    . 'WHERE IdPublication = '.$cPubId
                    . ' AND IdLanguage = '.$cLangId
                    . " AND Published = 'Y'";
                $data = $g_ado_db->GetRow($query);
                if (is_array($data) && sizeof($data) == 1) {
                    $cIssueNr = $data['Number'];
                }

                if (empty($cIssueNr)) {
                    CampTemplate::singleton()->trigger_error('not published issues');
                    return;
                }
            }
            // gets the template for the issue
            $template = CampSystem::GetIssueTemplate($cLangId, $cPubId, $cIssueNr);
            $this->setTemplate($template);
        }

        $this->m_validURI = true;
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
        if ($lookDir != $this->m_lookDir) {
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
        default:
            if (empty($p_param)) {
                $this->m_uriQuery = $this->m_query;
            }
            break;
        }

        // gets the template name from the context
        $context = CampTemplate::singleton()->context();
        $template = $context->$p_param->template->name;

        if (empty($template)) {
            CampTemplate::singleton()->trigger_error('Invalid template');
            return;
        }

        $this->m_uriPath = '/' . $this->m_lookDir . '/' . $template;
    } // fn buildURI

} // class CampURITemplatePath

?>