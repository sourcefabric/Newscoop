<?php
require_once 'index.php';
echo '<h3>Test</h3>';

$params = array(
    'fields' => array(
        'right_id',
        'right_define_name',
        'area_id',
        'area_define_name'
    ),
    'filters' => array(
        'perm_user_id' => 12
    )
);

$user_rights = $admin->perm->getRights($params);

var_dump($admin->getErrors());