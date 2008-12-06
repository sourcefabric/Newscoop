<?php
$info = array( 
    'name' => 'pear_quickform',
    'version' => '0.1',
    'label' => 'PEAR HTML QuickForm',
    'description' => 'This plugin provides the PEAR QuickForm library.',
    'userDefaultConfig' => array(), 
    'permissions' => array(),
);

// sets the PEAR local directory
if (!defined('PLUGINS_PEAR_QUCIKFORM_INCLUDE_PATH')) {
    define ('PLUGINS_PEAR_QUCIKFORM_INCLUDE_PATH', CS_PATH_PLUGINS.DIR_SEP.'pear_quickform'.DIR_SEP.'include'.DIR_SEP.'pear');
    set_include_path(PLUGINS_PEAR_QUCIKFORM_INCLUDE_PATH.PATH_SEPARATOR.get_include_path());
}
?>