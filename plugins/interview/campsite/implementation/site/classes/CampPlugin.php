<?php
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
    var $m_keyColumnNames = array('Name');

    var $m_dbTableName = 'Plugins';

    var $m_columnNames = array('Name', 'Version', 'Enabled');

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

    public function getAll()
    {
        global $g_ado_db;

        $CampPlugin = new CampPlugin();
        $tblname = $CampPlugin->m_dbTableName;

        $query = "SELECT Name
                  FROM   $tblname";

        $res = $g_ado_db->execute($query);
        $plugins = array();

        while ($row = $res->FetchRow()) {
            $plugins[] = new CampPlugin($row['Name']);;
        }

        return $plugins;
    }

    public function getName()
    {
        return $this->getProperty('Name');
    }

    public function getVersion()
    {
        return $this->getProperty('Version');
    }


    public function getEnabled()
    {
        return $this->getProperty('Enabled') == 1 ? true : false;
    }

    public function isPluginEnabled($p_name, $p_version = null)
    {
        $plugin = new CampPlugin($p_name, $p_version);

        return $plugin->getEnabled();
    }

    public function enable()
    {
        $this->setProperty('Enabled', 1);
    }

    public function disable()
    {
        $this->setProperty('Enabled', 0);
    }


    public function getPluginInfos()
    {
        static $plugin_infos;
        global $g_documentRoot;
        $directories = array('plugins');

        if (!is_array($plugin_infos)) {
            $plugin_infos = array();

            foreach ($directories as $dirName) {
                $dirName = "$g_documentRoot/$dirName";

                $handle=opendir($dirName);
                while ($entry = readdir($handle)) {
                    if ($entry != "." && $entry != ".." && $entry != '.svn' && is_dir("$dirName/$entry")) {
                        if (file_exists("$dirName/$entry/$entry.info.php")) {
                            include ("$dirName/$entry/$entry.info.php");
                            $plugin_infos[$entry] = $info;
                        }
                    }
                }
                closedir($handle);
            }
        }

        return $plugin_infos;
    }
    
    public function initPlugins4TemplateEngine()
    {
        $context = CampTemplate::singleton()->context();
        $infos = self::getPluginInfos();
        
        foreach ($infos as $info) {
            if (CampPlugin::isPluginEnabled($info['name'])) {
                User::registerDefaultConfig($info['userDefaultConfig']);
                
                foreach ($info['template_engine']['objecttypes'] as $objecttype) {
                    $context->registerObjectType($objecttype);
                }
                foreach ($info['template_engine']['listobjects'] as $listobject) {
                    $context->registerListObject($listobject);
                }
                if (isset($info['template_engine']['init_eval_code'])) {
                    eval($info['template_engine']['init_eval_code']);
                }
            }
        }
    }

    public function initPlugins4Admin()
    {
        global $no_menu_scripts;
        
        foreach (self::getPluginInfos() as $info) {
            if (CampPlugin::isPluginEnabled($info['name'])) {
                $no_menu_scripts = array_merge($no_menu_scripts, $info['no_menu_scripts']);
                User::registerDefaultConfig($info['userDefaultConfig']);
            }
        }
    }

    public function createPluginMenu(&$p_menu_root, $p_iconTemplateStr)
    {
        global $ADMIN;
        global $g_user;

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

        $plugin_infos = self::getPluginInfos();

        foreach ($plugin_infos as $info) {
            if (CampPlugin::isPluginEnabled($info['name'])) {
                $menu_plugin =& DynMenuItem::Create(getGS($info['menu']['label']),
                is_null($info['menu']['path']) ? null : "/$ADMIN/".$info['menu']['path'],
                array("icon" => sprintf($p_iconTemplateStr, $info['menu']['icon'])));
    
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
                $menu_modules->addItem($menu_plugin);
            }
        }
    }
}

?>
