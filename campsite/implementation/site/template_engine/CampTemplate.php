<?php
/**
 * @package Campsite
 */

//if (!defined('CAMPSITE')) {
//    exit();
//}

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
final class CampTemplate extends Smarty {

    /**
     * Holds instance of the class.
     *
     * @var object
     */
    private static $m_instance = null;


    private function __construct()
    {
        global $Campsite;

        parent::Smarty();

        $this->caching = $Campsite['smarty']['caching'];
        $this->cache_lifetime = $Campsite['smarty']['cache_lifetime'];
        $this->debugging = $Campsite['smarty']['debugging'];
        $this->force_compile = $Campsite['smarty']['force_compile'];
        $this->compile_check = $Campsite['smarty']['compile_check'];
        $this->use_sub_dirs = $Campsite['smarty']['use_sub_dirs'];

        $this->left_delimiter = '{{';
        $this->right_delimiter = '}}';

        $this->cache_dir = $Campsite['CAMPSITE_DIR'].'/var/smarty/cache';
        $this->config_dir = $Campsite['CAMPSITE_DIR'].'/var/smarty/configs';
        $this->template_dir = $Campsite['CAMPSITE_DIR'].'/var/smarty/templates';
        $this->compile_dir = $Campsite['CAMPSITE_DIR'].'/var/smarty/templates_c';
        $this->plugins_dir = array($Campsite['CAMPSITE_DIR'].'/var/smarty/camp_plugins',
                                   $Campsite['CAMPSITE_DIR'].'/var/smarty/plugins');
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


    public function trigger_error($p_message, $p_smarty = null)
    {
    	if (!is_null($p_smarty)) {
    		return $p_smarty->trigger_error($p_message);
    	} else {
    		return trigger_error("Campsite error: $p_message");
    	}
    }

} // class CampTemplate

?>