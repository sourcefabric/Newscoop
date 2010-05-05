<?php
// BC hack
if (!defined('PATH_SEPARATOR')) {
    if (defined('DIRECTORY_SEPARATOR') && DIRECTORY_SEPARATOR == '\\') {
        define('PATH_SEPARATOR', ';');
    } else {
        define('PATH_SEPARATOR', ':');
    }
}

// set this to the path in which the directory for liveuser resides
// more remove the following two lines to test LiveUser in the standard
// PEAR directory
# $path_to_liveuser_dir = 'PEAR/'.PATH_SEPARATOR;
# ini_set('include_path', $path_to_liveuser_dir.ini_get('include_path') );

require_once 'LiveUser.php';
require_once 'Log.php';

if (is_readable('Auth_XML.xml') && is_writable('Auth_XML.xml')) {
    $logger = Log::factory('win', 'liveuserlog');

    $liveuserConfig = array(
        'debug' => &$logger,
        'authContainers' => array(
            0 => array(
                'type' => 'XML',
                'expireTime'   => 3600,
                'idleTime'     => 1800,
                'passwordEncryptionMode' => 'MD5',
                'storage' => array(
                    'file' => 'Auth_XML.xml',
                    'alias' => array(
                        'auth_user_id' =>   'userId',
                        'passwd' =>         'password',
                        'lastlogin' =>      'lastLogin',
                        'is_active' =>      'isActive',
                        'name' =>           'name'
                    ),
                    'tables' => array(
                        'users' => array(
                            'fields' => array(
                                'lastlogin'         => false,
                                'is_active'         => false,
                                'owner_user_id'     => false,
                                'owner_group_id'    => false,
                                'name'              => false,
                            ),
                        ),
                    ),
                    'fields' => array(
                        'lastlogin'         => 'timestamp',
                        'is_active'         => 'boolean',
                        'owner_user_id'     => 'integer',
                        'owner_group_id'    => 'integer',
                        'name'              => 'text',
                    ),
                ),
            ),
        ),
    );
}

?>