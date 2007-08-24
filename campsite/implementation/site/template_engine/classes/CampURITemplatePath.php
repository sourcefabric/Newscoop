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

define('UP_LANGUAGE_ID', 'IdLanguage');
define('UP_PUBLICATION_ID', 'IdPublication');
define('UP_ISSUE_NR', 'NrIssue');
define('UP_SECTION_NR', 'NrSection');
define('UP_ARTICLE_NR', 'NrArticle');

/**
 * Class CampURITemplatePath
 */
class CampURITemplatePath extends CampURI {
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
     * Class constructor
     *
     * @param string $p_uri
     *      The requested URI
     */
    protected function __construct($p_uri = null)
    {
        parent::__construct($p_uri);
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
     * Gets the current template name.
     *
     * @return string
     *      The name of the template
     */
    public function getTemplate()
    {
        return $this->m_template;
    } // fn getTemplate


    /**
     * Sets the URL values.
     *
     * @return void
     */
    private function setURL()
    {
        // gets the publication object based on site name (URI host)
        $aliasArray = Alias::GetAliases(null, null, $this->getHost());
        if (is_array($aliasArray) && sizeof($aliasArray) == 1) {
            $aliasObj = $aliasArray[0];
            $cPubId = $aliasObj->getPublicationId();
            $pubObj = new Publication($cPubId);
            if (!is_object($pubObj) || !$pubObj->exists()) {
                $cPubId = 0;
                $pubObj = null;
            }
            $this->setQueryVar(UP_PUBLICATION_ID, $cPubId);
        }
        
        if (empty($cPubId)) {
            //return error/throw exception "not valid site alias"
        }

        // no path means we are at the home page
        if ($this->getPath() == '' || $this->getPath() == '/') {
            // sets the language identifier if necessary
            if ($this->getQueryVar(UP_LANGUAGE_ID) == 0) {
                $cLangId = $pubObj->getLanguageId();
                $this->setQueryVar(UP_LANGUAGE_ID, $cLangId);
            }
            // sets the issue number if necessary
            if ($this->getQueryVar(UP_ISSUE_NR) == 0) {
                $query = "SELECT MAX(Number) FROM Issues"
                   . " WHERE IdPublication = ".$cPubId." AND IdLanguage = ".$cLangId
                   . " AND Published = 'Y'";
                $data = $g_ado_db->GetRow($query);
                if (is_array($data) && sizeof($data) == 1) {
                    $cIssueNr = $data['Number'];
                }
                
                if (empty($cIssueNr)) {
                    //return error/throw exception "not published issues"
                }
            }
            // gets the template for the issue 
            $template = SomeClass::GetIssueTemplate($cLangId, $cPubId, $cIssueNr);
            $this->setTemplate($template);
        }
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
     * Builds the URI from object attributes.
     *
     * @return string $uri
     *      The URI
     */
    public function buildURI()
    {
        $uri = '';
        if (!empty($this->getTemplate())) {
            $uri = '/'.$this->m_lookDir.'/'.$this->getRequestURI();
        }

        return $uri;
    } // fn buildURI

} // class CampURITemplatePath

?>