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
     * Class constructor
     *
     * @param array $p_attributes
     */
    private function __construct($p_attributes = array())
    {
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
     *
     */
    public function singleton($p_attributes = array())
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampHTMLDocument($p_attributes);
        }

        return self::$m_instance;
    } // fn singleton


    /**
     *
     */
    public function getType()
    {
        return $this->m_type;
    } // fn getType


    /**
     *
     */
    public function getTitle()
    {
        return $this->m_title;
    } // fn getTitle


    /**
     *
     */
    public function getGenerator()
    {
        return $this->m_generator;
    } // fn getGenerator


    /**
     *
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
     *
     */
    public function getMimeType()
    {
        return $this->m_mime;
    } // fn getMimeType


    /**
     *
     */
    public function setMimeType($p_mime = 'text/html')
    {
        $this->m_mime = $p_mime;
    } // fn setMimeType


    /**
     *
     */
    public function getCharset()
    {
        return $this->m_charset;
    } // fn getCharset


    /**
     *
     */
    public function setCharset($p_charset = 'utf-8')
    {
        $this->m_charset = $p_charset;
    } // fn setCharset


    /**
     *
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
     *
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
     *
     */
    public function render($p_params)
    {
        $siteinfo = array();
        $context = $p_params['context'];
        $templates_dir = isset($p_params['templates_dir'])
                            ? $p_params['templates_dir'] : 'templates';
        $template = $p_params['template'];

        if (!file_exists($templates_dir.DIR_SEP.$template)) {
            $template = '_campsite_error.tpl';
        }

        $siteinfo['templates_path'] = $templates_dir;
        $siteinfo['title'] = $this->getTitle();
        $siteinfo['content_type'] = $this->getMetaTag('Content-Type', true);
        $siteinfo['generator'] = $this->getGenerator();
        $siteinfo['keywords'] = $this->getMetaTag('keywords');
        $siteinfo['description'] = $this->getMetaTag('description');

        $tpl = CampTemplate::singleton();
        $tpl->assign('campsite', $context);
        $tpl->assign('siteinfo', $siteinfo);

        $tpl->display($template);
    } // fn render

} // class CampHTMLDocument

?>