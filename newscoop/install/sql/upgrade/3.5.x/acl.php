<?php
/**
 * Populates ACL tables
 */

require_once dirname(__FILE__) . '/../../../../db_connect.php';
require_once dirname(__FILE__) . '/../../../../library/Newscoop/Utils/PermissionToAcl.php';

use Newscoop\Utils\PermissionToAcl;

$sql = 'SELECT right_define_name
        FROM liveuser_rights
        ORDER BY right_id';
$rows = $g_ado_db->GetAll($sql);

// process
$acl = array();
foreach ($rows as $row) {
    $right = $row['right_define_name'];
    list($resource, $action) = PermissionToAcl::translate($right);
    if (!isset($acl[$resource])) {
        $acl[$resource] = array();
    }
    $acl[$resource][] = $action;
}

// post process
$filters = array(
    'Audioclip',
);

foreach ($filters as $filter) {
    unset($acl[$filter]);
}

// insert resources
$resources = array_keys($acl);
sort($resources);
$g_ado_db->Execute('TRUNCATE acl_resource');
$sql = 'INSERT INTO acl_resource (name) VALUES ("' . implode('"), ("', $resources) . '")';
$g_ado_db->Execute($sql);

// insert actions
$actions = array();
$g_ado_db->Execute('TRUNCATE acl_action');
$g_ado_db->Execute('TRUNCATE acl_resource_action');
foreach ($acl as $resource => $resourceActions) {
    $resourceId = array_search($resource, $resources) + 1;

    foreach ($resourceActions as $action) {
        if (!isset($actions[$action])) { // save action for first time
            $sql = "INSERT INTO acl_action (name) VALUES('$action')";
            $g_ado_db->Execute($sql);
            $actions[$action] = sizeof($actions) + 1;
        }

        // save resource/action pair
        $actionId = $actions[$action];
        $sql = "INSERT INTO acl_resource_action (resource_id, action_id) VALUES($resourceId, $actionId)";
        $g_ado_db->Execute($sql);
    }
}

/** @todo populate rules */
