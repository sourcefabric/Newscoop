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

require_once($g_documentRoot.'/classes/CampBaseCache.php');


 /**
 * @package Campsite
 */
class CampCache extends CampBaseCache {
    /**
     * Holds instance of the class.
     *
     * @var object
     */
    private static $m_instance = null;


    /**
     *
     */
    private function __construct()
    {
        $this->init();
    } // fn __construct


    /**
     * Singleton function that returns the global class object.
     *
     * @return CampCache
     */
    public static function singleton()
    {
        if (!isset(self::$m_instance)) {
            self::$m_instance = new CampCache();
        }

        return self::$m_instance;
    } // fn singleton


    /**
     *
     */
    public function add($p_id, $p_data, $p_group, $p_expire = 0)
    {
        return $this->addObject($p_id, $p_data, $p_group, $p_expire);
    } // fn add


    /**
     *
     */
    public function set($p_id, $p_data, $p_group, $p_expire = 0)
    {
        return $this->setObject($p_id, $p_data, $p_group, $p_expire);
    } // fn set


    /**
     *
     */
    public function get($p_id, $p_group)
    {
        return $this->getObject($p_id, $p_group);
    } // fn get


    /**
     *
     */
    public function delete($p_id, $p_group, $p_force = false)
    {
        return $this->deleteObject($p_id, $p_group, $p_force);
    } // fn delete

} // class CampCache

?>