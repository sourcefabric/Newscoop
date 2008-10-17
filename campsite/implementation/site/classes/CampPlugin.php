<?php
define('PLUGINS_DIR', 'plugins');
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <devel@yellowsunshine.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */


/**
 * Class CampPlugin
 */


class CampPlugin extends DatabaseObject {
    public $m_keyColumnNames = array('Name');

    public $m_dbTableName = 'Plugins';

    public $m_columnNames = array('Name', 'Version', 'Enabled');

    static private $m_allPlugins = null;

    static protected $m_pluginInfos = null;

    public function CampPlugin($p_name = null, $p_version = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['Name'] = $p_name;

        if (!is_null($p_version)) {
            $this->m_data['Version'] = $p_version;
        }
        if (!is_null($p_name)) {
            $this->fetch();
        }
    } // constructor

    public function create($p_name, $p_version, $p_enabled = true)
    {
        // Create the record
        $values = array(
            'Name' => $p_name,
            'Version' => $p_version,
            'Enabled' => $p_enabled ? 1 : 0
        );


        $success = parent::create($values);
        if (!$success) {
            return false;
        }
    }

    static public function GetAll()
    {
        global $g_ado_db;

        if (!is_null(self::$m_allPlugins)) {
            return self::$m_allPlugins;
        }

        $CampPlugin = new CampPlugin();
        $tblname = $CampPlugin->m_dbTableName;

        $query = "SELECT Name
                  FROM   $tblname";

        $res = $g_ado_db->execute($query);
        if (!$res) {
            return array();
        }
        self::$m_allPlugins = array();

        while ($row = $res->FetchRow()) {
            self::$m_allPlugins[] = new CampPlugin($row['Name']);;
        }

        return self::$m_allPlugins;
    }

    static public function GetEnabled()
    {
        $plugins = array();

        foreach (self::GetAll() as $CampPlugin) {
            if ($CampPlugin->isEnabled()) {
                $plugins[] = $CampPlugin;
            }
        }
        return $plugins;
    }

    public function getBasePath()
    {
        return PLUGINS_DIR.'/'.$this->getName();
    }

    public function getName()
    {
        return $this->getProperty('Name');
    }

    public function getVersion()
    {
        return $this->getProperty('Version');
    }

    public function isEnabled()
    {
        return $this->getProperty('Enabled') == 1 ? true : false;
    }

    static public function IsPluginEnabled($p_name, $p_version = null)
    {
        $plugin = new CampPlugin($p_name, $p_version);

        return $plugin->isEnabled();
    }

    public function install()
    {
        $info = $this->getPluginInfo();
        if (function_exists($info['install'])) {
            call_user_func($info['install']);
        }
    }
    
    public function enable()
    {
        $this->setProperty('Enabled', 1);

        $info = $this->getPluginInfo();
        if (function_exists($info['enable'])) {
            call_user_func($info['enable']);
        }
    }

    public function disable()
    {
        $this->setProperty('Enabled', 0);

        $info = $this->getPluginInfo();
        if (function_exists($info['disable'])) {
            call_user_func($info['disable']);
        }
    }
    
    public function uninstall()
    {
        $info = $this->getPluginInfo();
        if (function_exists($info['uninstall'])) {
            call_user_func($info['uninstall']);
        }
        
        self::ClearPluginInfos();        
        $this->delete();   
    }

    static public function GetPluginInfos()
    {
        global $g_documentRoot;
        
        $directories = array(PLUGINS_DIR);

        if (!is_array(self::$m_pluginInfos)) {
            self::$m_pluginInfos = array();

            foreach ($directories as $dirName) {
                $dirName = "$g_documentRoot/$dirName";
                if (!is_dir($dirName)) {
                    continue;
                }

                $handle=opendir($dirName);
                while ($entry = readdir($handle)) {
                    if ($entry != "." && $entry != ".." && $entry != '.svn' && is_dir("$dirName/$entry")) {
                        if (file_exists("$dirName/$entry/$entry.info.php")) {
                            include ("$dirName/$entry/$entry.info.php");
                            self::$m_pluginInfos[$entry] = $info;
                        }
                    }
                }
                closedir($handle);
            }
        }

        return self::$m_pluginInfos;
    }
    
    static public function ClearPluginInfos()
    {
        self::$m_pluginInfos = null;
    }

    public function getPluginInfo($p_plugin_name = '')
    {
        if (!empty($p_plugin_name)) {
            $name = $p_plugin_name;
        } elseif (isset($this) && is_a($this, 'CampPlugin')) {
            $name = $this->getName();
        } else {
            return false;
        }

        $infos = self::GetPluginInfos();
        $info = $infos[$name];

        return $info;
    }

    static public function ExtendNoMenuScripts(&$p_no_menu_scripts)
    {
        foreach (self::GetPluginInfos() as $info) {
            if (CampPlugin::IsPluginEnabled($info['name'])) {
                $p_no_menu_scripts = array_merge($p_no_menu_scripts, $info['no_menu_scripts']);
            }
        }
    }

    static public function CreatePluginMenu(&$p_menu_root, $p_iconTemplateStr)
    {
        global $ADMIN;
        global $g_user;
        
        $root_menu = false;
        $plugin_infos = self::GetPluginInfos();
        
        if ($g_user->hasPermission('plugin_manager')) {
            $root_menu = true;   
        }

        
        foreach ($plugin_infos as $info) {
            if (CampPlugin::IsPluginEnabled($info['name'])) {
                if (isset($info['menu']['permission']) && $g_user->hasPermission($info['menu']['permission'])) {
                    $root_menu = true;
                } elseif (is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menu_info) {
                        if ($g_user->hasPermission($menu_info['permission'])) {
                            $root_menu = true;
                        }
                    }
                }
            }
        }
        
        if (empty($root_menu)) {
            return;   
        }     
                    
        $p_menu_root->addSplit();
        $menu_modules =& DynMenuItem::Create("Plugins", "",
        array("icon" => sprintf($p_iconTemplateStr, "plugin.png"), "id" => "plugins"));
        $p_menu_root->addItem($menu_modules);

        if ($g_user->hasPermission("plugin_manager")) {
            $menu_item =& DynMenuItem::Create(getGS('Manage Plugins'),
            "/$ADMIN/plugins/manage.php",
            array("icon" => sprintf($p_iconTemplateStr, "configure.png")));
            $menu_modules->addItem($menu_item);

        }

        foreach ($plugin_infos as $info) {
            if (CampPlugin::IsPluginEnabled($info['name'])) {
                $menu_plugin = null;
                $parent_menu = false;

                if (isset($info['menu']['permission']) && $g_user->hasPermission($info['menu']['permission'])) {
                    $parent_menu = true;
                } elseif (is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menu_info) {
                        if ($g_user->hasPermission($menu_info['permission'])) {
                            $parent_menu = true;
                        }
                    }
                }

                if ($parent_menu) {
                    $menu_plugin =& DynMenuItem::Create(getGS($info['menu']['label']),
                    is_null($info['menu']['path']) ? null : "/$ADMIN/".$info['menu']['path'],
                    array("icon" => sprintf($p_iconTemplateStr, $info['menu']['icon'])));
                }

                if (is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menu_info) {
                        if ($g_user->hasPermission($menu_info['permission'])) {
                            $menu_item =& DynMenuItem::Create(getGS($menu_info['label']),
                            is_null($menu_info['path']) ? null : "/$ADMIN/".$menu_info['path'],
                            array("icon" => sprintf($p_iconTemplateStr, $menu_info['icon'])));
                            $menu_plugin->addItem($menu_item);
                        }
                    }
                }

                if (is_object($menu_plugin)) {
                    $menu_modules->addItem($menu_plugin);
                }
            }
        }
    }

    static public function ExtractPackage($p_uploaded_package, &$p_log = null)
    {
        global $g_documentRoot;
        
        $plugin_name = false;

        require_once('Archive/Tar.php');
        $tar = new Archive_Tar($p_uploaded_package);
        
        
        if (($file_list = $tar->ListContent()) == 0) {
            $p_log = getGS('The uploaded file format is unsupported.');
            return false;
        } else {
            foreach ($file_list as $v) {
                
                if (preg_match('/[^\/]+\/([^.]+)\.info\.php/', $v['filename'], $matches)) {
                    $plugin_name = $matches[1];    
                }
                
                #$p_log .= sprintf("Name: %s  Size: %d   modtime: %s mode: %s<br>", $v['filename'], $v['size'], $v['mtime'], $v['mode']);
            }
        }
        
        if ($plugin_name === false) {
            $p_log = getGS('The uploaded archive does not contain an valid campsite plugin.');
            return false;    
        }
        
        $tar->extract($g_documentRoot.DIR_SEP.PLUGINS_DIR);
        
        CampPlugin::clearPluginInfos();
    }
    
    public static function PluginAdminHooks($p_filename, $p_area=null)
    {
        global $ADMIN, $ADMIN_DIR, $Campsite, $g_user;
        
        $paths = array();
        
        $filename = realpath($p_filename);
        $admin_path = realpath(CS_PATH_SITE.DIR_SEP.$ADMIN_DIR);
        $script = str_replace($admin_path, '', $filename);
        
        foreach (self::GetEnabled() as $plugin) {
            $filepath = $plugin->getBasePath().DIR_SEP.'admin-files'.DIR_SEP.'include'.DIR_SEP.$script;
            if (file_exists($filepath))  {
                include $filepath;   
            }  
        }  
    }
}

?>
