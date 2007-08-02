<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/include/smarty/Smarty.class.php');


/**
 * @package Campsite
 */
final class CampTemplate extends Smarty
{

    /**
     * Holds instance of the class.
     *
     * @var object
     */
	private static $m_instance = null;


	/**
	 * Holds the context object;
	 *
	 * @var object
	 */
	private $m_context = null;


    private function __construct()
    {
        parent::Smarty();

        $config = CampSite::GetConfig();

        $this->caching = $config->getSetting('smarty.caching');
        $this->debugging = $config->getSetting('smarty.debugging');
        $this->force_compile = $config->getSetting('smarty.force_compile');
        $this->compile_check = $config->getSetting('smarty.compile_check');
        $this->use_sub_dirs = $config->getSetting('smarty.use_subdirs');

        $this->left_delimiter = $config->getSetting('smarty.left_delimeter');
        $this->right_delimiter = $config->getSetting('smarty.right_delimeter');

        $this->cache_dir = CS_PATH_SMARTY.DIR_SEP.'cache';
        $this->config_dir = CS_PATH_SMARTY.DIR_SEP.'configs';
        $this->plugins_dir = array(CS_PATH_SMARTY.DIR_SEP.'camp_plugins',
                                   CS_PATH_SMARTY.DIR_SEP.'plugins');
        $this->template_dir = CS_PATH_SMARTY_TEMPLATES.DIR_SEP.$config->getSetting('site.theme');
        $this->compile_dir = CS_PATH_SMARTY.DIR_SEP.'templates_c';
    } // fn __constructor


    /**
     * Singleton function that returns the global class object.
     *
     * @return object
     *    CampCache
     */
    public static function singleton()
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampTemplate();
        }

        return self::$m_instance;
    } // fn singleton


    /**
     * Returns the template context object.
     *
     * @return object
     */
    public function &context()
    {
    	if (!isset($this->m_context)) {
    		$this->m_context = new CampContext();
    	}
    	return $this->m_context;
    }


    /**
     * Inserts an error message into the errors list.
     *
     * @param string $p_message
     * @param object $p_smarty
     * @return void
     */
    public function trigger_error($p_message, $p_smarty = null)
    {
    	if (!is_null($p_smarty)) {
    		$p_smarty->trigger_error($p_message);
    	} else {
    		trigger_error("Campsite error: $p_message");
    	}
    }

} // class CampTemplate

?>