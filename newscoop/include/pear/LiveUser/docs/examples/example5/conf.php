<?php
require_once 'MDB2.php';
require_once 'LiveUser.php';
// Plase configure the following file according to your environment

//$dsn = '{dbtype}://{user}:{passwd}@{dbhost}/{dbname}';
$dsn = 'mysql://root:@localhost/liveuser_test_example5';

$db = MDB2::connect($dsn);

if (PEAR::isError($db)) {
    echo $db->getMessage() . ' ' . $db->getUserInfo();
}

$db->setFetchMode(MDB2_FETCHMODE_ASSOC);


$conf =
    array(
        'debug' => true,
        'session'  => array(
            'name'     => 'PHPSESSION',
            'varname'  => 'ludata'
        ),
        'login' => array(
            'force'    => false,
        ),
        'logout' => array(
            'destroy'  => true,
        ),
        'authContainers' => array(
            'DB' => array(
                'type'          => 'MDB2',
                'expireTime'    => 3600,
                'idleTime'      => 1800,
                'storage' => array(
                    'dsn' => $dsn,
                    'alias' => array(
                        'lastlogin' => 'lastlogin',
                        'is_active' => 'is_active',
                    ),
                    'fields' => array(
                        'lastlogin' => 'timestamp',
                        'is_active' => 'boolean',
                    ),
                    'tables' => array(
                        'users' => array(
                            'fields' => array(
                                'lastlogin' => false,
                                'is_active' => false,
                            ),
                        ),
                    ),
                )
            )
        ),
    'permContainer' => array(
        'type' => 'Medium',
        'storage' => array('MDB2' => array('dsn' => $dsn, 'prefix' => 'liveuser_')),
    ),
);
