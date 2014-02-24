<?php
/**
 * @package Newscoop
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Class CampTemplate
 */
final class CampTemplate extends Smarty
{
    const PLUGINS = '/../include/smarty/campsite_plugins';
    const SCRIPTS = '/views/scripts';

    /** @var CampTemplate */
    private static $m_instance = null;

    /** @var CampContext */
    private $m_context;

    /** @var bool */
    private $m_preview = false;

    /** @var array */
    public $campsiteVector = array();

    /**
     */
    public function __construct()
    {
        parent::__construct();

        $config = CampSite::GetConfigInstance();

        $this->debugging = $config->getSetting('smarty.debugging');
        $this->force_compile = $config->getSetting('smarty.force_compile');
        $this->compile_check = $config->getSetting('smarty.compile_check');
        $this->use_sub_dirs = $config->getSetting('smarty.use_subdirs');

        // cache settings
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $cacheHandler = $preferencesService->TemplateCacheHandler;
        $auth = Zend_Auth::getInstance();
        if ($cacheHandler) {
            $this->caching = 1;
            $this->caching_type = 'newscoop';
            CampTemplateCache::factory();
        } else {
            $this->caching = 0;
        }

        if (self::isDevelopment()) {
            $this->force_compile = true;
        }

        // define dynamic uncached block
        require_once APPLICATION_PATH . self::PLUGINS . '/block.dynamic.php';
        $this->registerPlugin('block', 'dynamic', 'smarty_block_dynamic', false);

        // define render function
        require_once APPLICATION_PATH . self::PLUGINS . '/function.render.php';
        $this->registerPlugin('function', 'render', 'smarty_function_render', false);

        // define translate modifier
        require_once APPLICATION_PATH . self::PLUGINS . '/modifier.translate.php';
        $this->registerPlugin('modifier', 'translate', 'smarty_modifier_translate', false);

        $this->left_delimiter = '{{';
        $this->right_delimiter = '}}';
        $this->auto_literal = false;

        $this->cache_dir = APPLICATION_PATH . '/../cache';
        $this->compile_dir = APPLICATION_PATH . '/../cache';

        $this->plugins_dir = array_merge(
            (array) $this->plugins_dir,
            array(APPLICATION_PATH . self::PLUGINS),
            self::getPluginsPluginsDir()
        );

        $this->setTemplateDir(array(
            APPLICATION_PATH . '/../themes/',
            APPLICATION_PATH . '/../themes/system_templates/',
            APPLICATION_PATH . self::SCRIPTS,
        ));

        $this->assign('view', \Zend_Registry::get('container')->get('view'));
        $this->assign('userindex', false);
        $this->assign('user', new MetaUser());
    }

    /**
     * Get plugins plugins dir
     *
     * @return array
     */
    public static function getPluginsPluginsDir()
    {
        $pluginsManager = \Zend_Registry::get('container')->getService('newscoop.plugins.manager');
        $availablePlugins = $pluginsManager->getInstalledPlugins();
        $dirs = array();
        foreach ($availablePlugins as $plugin) {
            $pluginPath = explode('\\', $plugin);
            $directoryPath = realpath(__DIR__ . '/../../plugins/'.$pluginPath[0].'/'.$pluginPath[1].'/Resources/smartyPlugins');
            if ($directoryPath) {
                $dirs[] = $directoryPath;
            }
        }

        //legacy plugins
        foreach (CampPlugin::GetEnabled() as $CampPlugin) {
            $dirs[] = CS_PATH_SITE . "/{$CampPlugin->getBasePath()}/smarty_camp_plugins";
        }

        return $dirs;
    }

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
    }

    /**
     * Test if context is initialized
     *
     * @return bool
     */
    public function contextInitialized()
    {
        return !is_null($this->m_context);
    }

    /**
     * Returns the template context object.
     *
     * @return CampContext object
     */
    public function context()
    {
        if (!isset($this->m_context)) {
            $this->refreshContext();
        }

        return $this->m_context;
    }

    public function refreshContext()
    {
        $this->m_context = new CampContext();
        $this->m_preview = $this->m_context->preview;
    }

    /**
     * Clear cache
     *
     * @return void
     */
    public function clearCache($template_name = null, $cache_id = NULL, $compile_id = NULL, $exp_time = NULL, $type = NULL)
    {
        $this->clearCompiledTemplate();
        $this->clearAllCache();
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
            trigger_error("Newscoop error: $p_message");
        }
    }

    /**
     * get a concrete filename for automagically created content
     *
     * @param string $auto_base
     * @param string $auto_source
     * @param string $auto_id
     *
     * @return string
     */
    public function _get_auto_filename($auto_base, $auto_source = null, $auto_id = null)
    {
        $show_spec = '';
        if (isset($this->m_context)) {
            $show_spec .= hash('sha1', ($this->m_context->template ? $this->m_context->template->theme_dir : ''));
        }

        return parent::_get_auto_filename($auto_base, $auto_source, $auto_id) . '%%' . $show_spec;
    }

    /**
     * Test if is development environment
     *
     * @return bool
     */
    private static function isDevelopment()
    {
        return false;//return defined('APPLICATION_ENV') && APPLICATION_ENV == 'development';
    }
}
