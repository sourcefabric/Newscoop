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

require_once('smarty/libs/Smarty.class.php');

/**
 * Class CampTemplate
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


    private $m_preview = false;

    public $campsiteVector = array();

    private function __construct()
    {
        parent::Smarty();

        $config = CampSite::GetConfigInstance();

        $this->debugging = $config->getSetting('smarty.debugging');
        $this->force_compile = $config->getSetting('smarty.force_compile');
        $this->compile_check = $config->getSetting('smarty.compile_check');
        $this->use_sub_dirs = $config->getSetting('smarty.use_subdirs');

        // cache settings
        $cacheHandler = SystemPref::Get('TemplateCacheHandler');
        if ($cacheHandler) {
            $this->caching = 1;
            $this->cache_handler_func = "TemplateCacheHandler_$cacheHandler::handler";
            require_once CS_PATH_SITE.DIR_SEP.'classes'.DIR_SEP.'cache'.DIR_SEP."TemplateCacheHandler_$cacheHandler.php";
        } else {
            $this->caching = 0;
        }

        // define dynamic uncached block
        require_once CS_PATH_SMARTY.DIR_SEP.'campsite_plugins/block.dynamic.php';
        $this->register_block('dynamic', 'smarty_block_dynamic', false);

        // define render function
        require_once CS_PATH_SMARTY.DIR_SEP.'campsite_plugins/function.render.php';
        $this->register_function('render', 'smarty_function_render', false);

        $this->left_delimiter = $config->getSetting('smarty.left_delimeter');
        $this->right_delimiter = $config->getSetting('smarty.right_delimeter');

        $this->cache_dir = CS_PATH_SITE.DIR_SEP.'cache';
        $this->config_dir = CS_PATH_SMARTY.DIR_SEP.'configs';

        $plugin_smarty_camp_plugin_paths = array();
        foreach (CampPlugin::GetEnabled() as $CampPlugin) {
            $plugin_smarty_camp_plugin_paths[] = CS_PATH_SITE.DIR_SEP.$CampPlugin->getBasePath().DIR_SEP.'smarty_camp_plugins';
        }

        $this->plugins_dir = array_merge($this->plugins_dir,
        								 array(CS_PATH_SMARTY.DIR_SEP.'campsite_plugins'),
                                         $plugin_smarty_camp_plugin_paths);
        $this->template_dir = CS_PATH_TEMPLATES;
        $this->compile_dir = CS_PATH_SITE.DIR_SEP.'cache';
    } // fn __constructor


    /**
     * Singleton function that returns the global class object.
     *
     * @return CampTemplate object
     */
    public static function singleton()
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampTemplate();
        }

        return self::$m_instance;
    } // fn singleton


    public function contextInitialized()
    {
        return !is_null($this->m_context);
    }


    /**
     * Returns the template context object.
     *
     * @return CampContext object
     */
    public function &context()
    {
        if (!isset($this->m_context)) {
            $this->m_context = new CampContext();
            $this->m_preview = $this->m_context->preview;
        }
        return $this->m_context;
    } // fn context


    public function setTemplateDir($p_dir)
    {
        $this->template_dir = $p_dir;
    } // fn setTemplateDir


    public function clearCache()
    {
        $this->clear_compiled_tpl();
        $this->clear_all_cache();
    }


    /**
     * Inserts an error message into the errors list.
     *
     * @param string $p_message
     * @param object $p_smarty
     *
     * @return void
     */
    public function trigger_error($p_message, $p_smarty = null)
    {
        if (!self::singleton()->m_preview) {
            return;
        }
        if (is_object($p_smarty)) {
            $p_smarty->trigger_error($p_message);
        } else {
            trigger_error("Campsite error: $p_message");
        }
    } // fn trigger_error

} // class CampTemplate

?>
