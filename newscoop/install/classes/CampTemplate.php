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

    public function __construct()
    {
        parent::__construct();

        $this->caching = false;
        $this->debugging = false;
        $this->force_compile = true;
        $this->compile_check = false;
        $this->use_sub_dirs = false;
        $this->auto_literal = false;

        $this->left_delimiter = '{{';
        $this->right_delimiter = '}}';

        $this->addTemplateDir(array(
            CS_INSTALL_DIR . DIR_SEP . 'templates',
            CS_PATH_SITE . '/themes/',
            CS_PATH_SITE . '/themes/unassigned/system_templates/',
        ));
        $this->setCompileDir(__DIR__ . '/../../cache');
        $this->addPluginsDir(CS_PATH_SMARTY.DIR_SEP.'campsite_plugins');
        $this->setCacheDir(CS_PATH_SITE.DIR_SEP.'cache');
        $this->setConfigDir(CS_PATH_SMARTY.DIR_SEP.'configs');
    }

    /**
     * Singleton function that returns the global class object.
     *
     * @return object
     *    CampTemplate
     */
    public static function singleton()
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampTemplate();
        }

        return self::$m_instance;
    }

    public function clearCache($template_name = null, $cache_id = NULL, $compile_id = NULL, $exp_time = NULL, $type = NULL)
    {
        $this->clearCompiledTemplate();
        return $this;
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
    	if (is_object($p_smarty)) {
    		$p_smarty->trigger_error($p_message);
    	} else {
    		trigger_error("Newscoop error: $p_message");
    	}
    }
}
