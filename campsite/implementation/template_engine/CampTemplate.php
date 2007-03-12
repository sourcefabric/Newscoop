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
class CampTemplate extends Smarty {

    public function __construct()
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
        $this->plugins_dir = array(
                                   $Campsite['CAMPSITE_DIR'].'/var/smarty/camp_plugins',
                                   $Campsite['CAMPSITE_DIR'].'/var/smarty/plugins'
                                   );
    } // fn __constructor

} // class CampTemplate

?>