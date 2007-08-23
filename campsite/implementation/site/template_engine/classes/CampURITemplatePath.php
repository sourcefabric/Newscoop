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
require_once($g_documentRoot.'/template_engine/classes/CampURI.php');


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
     * Template name
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
     */
    public function parse()
    {
        $template = $this->readTemplate();
        $this->setTemplate($template);
    } // fn parse


    /**
     *
     */
    public function getTemplate()
    {
        return $this->m_template;
    } // fn getTemplate


    /**
     *
     */
    private function setURL()
    {
    
    } // fn setURL


    /**
     * Sets the template name.
     *
     * @param string $p_value
     *      The template name
     *
     * @return void
     */
    private function setTemplateName($p_value)
    {
        if (!$this->isValidTemplate($p_value)) {
            return false;
        }
        
        $this->m_template = $p_value;
    } // fn setTemplateName
    

    /**
     * Returns the template name from URI.
     *
     * @return string
     *      null on failure, otherwise the template name
     */
    private function readTemplate()
    {
        $trimmedPath = trim($this->m_path, '/');
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
        $uri = '/'.$this->m_lookDir.'/'.$this->getTemplate().'?'.$this->getQuery();
        return $uri;
    } // fn buildURI

} // class CampURITemplatePath

?>