<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Class CampHTMLDocument
 */
final class CampHTMLDocument
{
    /**
     * @var string
     */
    private $m_type = 'html';

    /**
     * @var string
     */
    private $m_title = null;

    /**
     * @var string
     */
    private $m_generator = 'Campsite 3.0';

    /**
     * @var string
     */
    private $m_mime = 'text/html';

    /**
     * @var string
     */
    private $m_charset = 'utf-8';

    /**
     * @var string
     */
    private $m_language = 'en';

    /**
     * @var array
     */
    private $m_metaTags = array();

    /**
     * @var
     */
    private $m_output = null;

    /**
     * Holds instance of the class.
     *
     * @var object
     */
    private static $m_instance = null;

    /**
     * @var object
     */
    private $m_config = null;

    /**
     * Class constructor
     *
     * @param array $p_attributes
     */
    private function __construct($p_attributes = array())
    {
        $this->m_config = CampConfig::singleton();

        if (isset($p_attributes['type'])) {
            $this->m_type = $p_attributes['type'];
        } else {
            $this->m_type = 'html';
        }

        if (isset($p_attributes['charset'])) {
            $this->m_charset = $p_attributes['charset'];
        } else {
            $this->m_charset = 'utf-8';
        }

        if (isset($p_attributes['language'])) {
            $this->m_language = $p_attributes['language'];
        }

        //set default document metadata
        $this->setMetaTag('Content-Type',
                           $this->m_mime.'; charset='.$this->m_charset, true);
        $this->setMetaTag('robots', 'index, follow');
    } // fn __construct


    /**
     * Builds an instance object of this class only if there is no one.
     *
     * @param array $p_attributes
     *
     * @return object
     */
    public static function singleton($p_attributes = array())
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampHTMLDocument($p_attributes);
        }

        return self::$m_instance;
    } // fn singleton


    /**
     * Returns the type of document.
     *
     * @return string
     */
    public function getType()
    {
        return $this->m_type;
    } // fn getType


    /**
     * Returns the title of the document.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->m_title;
    } // fn getTitle


    /**
     * Returns the page generator.
     *
     * @return string
     */
    public function getGenerator()
    {
        return $this->m_generator;
    } // fn getGenerator


    /**
     * Sets the document title.
     *
     * @param string $p_title
     *
     * @return void
     */
    public function setTitle($p_title = null)
    {
        $config = CampSite::GetConfigInstance();

        if($config->getSetting('site.online') == 'N') {
            $p_title .= ' [ Offline ]';
        }

        $this->m_title = $p_title;
    } // fn setTitle


    /**
     * Returns the MIME type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->m_mime;
    } // fn getMimeType


    /**
     * Sets the MIME type for the document.
     *
     * @param string $p_mime
     *
     * @return void
     */
    public function setMimeType($p_mime = 'text/html')
    {
        $this->m_mime = $p_mime;
    } // fn setMimeType


    /**
     * Returns the charset.
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->m_charset;
    } // fn getCharset


    /**
     * Sets the charset.
     *
     * @param string $p_charset
     *
     * @return void
     */
    public function setCharset($p_charset = 'utf-8')
    {
        $this->m_charset = $p_charset;
    } // fn setCharset


    /**
     * Returns the requested META tag.
     *
     * @param string $p_name
     *      The name of the META tag to be retrieved
     * @param boolean $p_httpEquiv
     *      Whether it is a http-equiv META tag or not
     *
     * @return string $tag
     */
    function getMetaTag($p_name, $p_httpEquiv = false)
    {
        $tag = '';
        if ($p_httpEquiv == true) {
            $tag = isset($this->m_metaTags['http-equiv'][$p_name])
                ? $this->m_metaTags['http-equiv'][$p_name] : null;
        } else {
            $tag = isset($this->m_metaTags['name'][$p_name])
                ? $this->m_metaTags['name'][$p_name] : null;
        }

        return $tag;
    } // fn getMetaTag


    /**
     * Sets the given META tag.
     *
     * @param string $p_name
     *      The name of the META tag to be retrieved
     * @param mixed $p_value
     *      The value for the META tag
     * @param boolean $p_httpEquiv
     *      Whether it is a http-equiv META tag or not
     *
     * @return void
     */
    public function setMetaTag($p_name, $p_value, $p_httpEquiv = false)
    {
        if ($p_httpEquiv == true) {
            $this->m_metaTags['http-equiv'][$p_name] = $p_value;
        } else {
            $this->m_metaTags['name'][$p_name] = $p_value;
        }
    } // fn setMetaTag


    /**
     * Renders the document.
     *
     * Displays the document after parsing it.
     *
     * @param array $p_params
     *
     * @return void
     */
    public function render($p_params)
    {
        $siteinfo = array();
        $context = $p_params['context'];
        $template = $p_params['template'];

        $siteinfo['info_message'] = isset($p_params['info_message']) ? $p_params['info_message'] : null;
        $siteinfo['error_message'] = isset($p_params['error_message']) ? $p_params['error_message'] : null;
        $siteinfo['templates_path'] = isset($p_params['templates_dir'])
                            ? $p_params['templates_dir'] : CS_TEMPLATES_DIR;
        $siteinfo['title'] = $this->getTitle();
        $siteinfo['content_type'] = $this->getMetaTag('Content-Type', true);
        $siteinfo['generator'] = $this->getGenerator();
        $siteinfo['keywords'] = $this->getMetaTag('keywords');
        $siteinfo['description'] = $this->getMetaTag('description');
        $tpl = CampTemplate::singleton();
        $tpl->template_dir = array_unique($tpl->template_dir);

        array_unshift($tpl->template_dir, CS_PATH_SITE . DIR_SEP . $siteinfo['templates_path']);
        if (!$template) {
            $siteinfo['error_message'] = "No template set for display.";
        } elseif (!$this->templateExists($template, $tpl)) {
            $siteinfo['error_message'] = "The template '$template' does not exist in the templates directory.";
        }

        if (!is_null($siteinfo['error_message'])) {
            $siteinfo['templates_path'] = CS_TEMPLATES_DIR . DIR_SEP . CS_SYS_TEMPLATES_DIR;
            $template = '_campsite_error.tpl';
            array_unshift($tpl->template_dir, CS_PATH_SITE . DIR_SEP . $siteinfo['templates_path']);
        }

        $subdir = $this->m_config->getSetting('SUBDIR');
        if (!empty($subdir)) {
            $siteinfo['templates_path'] = substr($subdir, 1) . '/' . $siteinfo['templates_path'];
        }

        $tpl->assign('gimme', $context);
        $tpl->assign('siteinfo', $siteinfo);
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');

        // on template caching add additional info
        if ($preferencesService->TemplateCacheHandler) {
            $uri = CampSite::GetURIInstance();
            $tpl->campsiteVector = $uri->getCampsiteVector();
            $templateObj = new Template(CampSite::GetURIInstance()->getThemePath() . ltrim($template, '/'));
            $tpl->cache_lifetime = (int)$templateObj->getCacheLifetime();
        }

        try {
            $tpl->display($template);
        } catch (Exception $e) {
            CampTemplate::trigger_error($e->getMessage(), $tpl);
        }
    } // fn render

    /**
     * Test if template exists
     *
     * @param string $template
     * @param Smarty $smarty
     * @return bool
     */
    private function templateExists($template, $smarty)
    {
        foreach ($smarty->template_dir as $dir) {
            $filePath = "$dir/" . ltrim($template, '/');
            if (file_exists($filePath)) {
                return true;
            }
        }

        return false;
    }

} // class CampHTMLDocument

?>
