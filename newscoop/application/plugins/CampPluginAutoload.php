<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Camp plugin autoload
 */
class Application_Plugin_CampPluginAutoload extends Zend_Controller_Plugin_Abstract
{
    /**
     * Add registered plugins paths to include path
     */
    public function dispatchLoopStartup()
    {
        $includePaths = array(
            'classes',
            'template_engine/classes',
            'template_engine/metaclasses',
        );

        // add plugins to path
        foreach (CampPlugin::GetPluginsInfo(true) as $info) {
            $name = $info['name'];
            foreach ($includePaths as $path) {
                $includePath = APPLICATION_PATH . "/../plugins/$name/$path";
                $realpath = realpath($includePath);
                if ($realpath) {
                    set_include_path(implode(PATH_SEPARATOR, array(
                        get_include_path(),
                        $realpath,
                    )));
                }
            }
        }
    }
}
