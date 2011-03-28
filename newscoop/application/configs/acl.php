<?php
/**
 * Populates ACL tables
 */

global $g_ado_db;

$sql = 'SELECT right_define_name
        FROM liveuser_rights
        ORDER BY right_id';
$rows = $g_ado_db->GetAll($sql);

// process
$acl = array();
foreach ($rows as $row) {
    $right = $row['right_define_name'];
    list($resource, $action) = translate_right($right);
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
//$g_ado_db->Execute('TRUNCATE acl_resource');
$sql = 'INSERT INTO acl_resource (name) VALUES ("' . implode('", "', array_keys($acl)) . '")';
$g_ado_db->Execute($sql);

exit;

/**
 * Translate right string to resource - action pair
 *
 * @param string $right
 * @return array
 */
function translate_right($right)
{
    static $filters = array(
        'ToArticle' => '',
        'plugin_manager' => 'ManagePlugin',
        'MailNotify' => 'GetNotification',
        'Publish' => 'PublishArticle',
        'plugin_poll' => 'EnablePluginPoll',
        'SyncPhorumUsers' => 'SynchronizePhorumUsers',
        'Change' => 'Edit',
    );

    static $resources = array(
        'Editor',
        'Comment',
    );

    static $actions = array(
        'Add',
        'Edit',
        'Attach',
        'Clear',
        'Delete',
        'Manage',
        'Move',
        'View',
        'Translate',
        'Get',
        'Publish',
        'Enable',
        'Synchronize',
    );

    // filter right
    foreach ($filters as $search => $replace) {
        $right = str_replace($search, $replace, $right);
    }

    // try to find known resource
    foreach ($resources as $resource) {
        if (strpos($right, $resource) !== FALSE) {
            $action = str_replace($resource, '', $right);
            return array($resource, $action);
        }
    }

    // try to find known action
    foreach ($actions as $action) {
        if (strpos($right, $action) !== FALSE) {
            $resource = str_replace($action, '', $right);
            return array($resource, $action);
        }
    }

    // try plugins
    $right_ary = explode('_', $right);
    if (sizeof($right_ary) == 3) {
        $right_ary = array_map('ucfirst', $right_ary);
        $resource = $right_ary[0] . $right_ary[1];
        $action = $right_ary[2];
        return array($resource, $action);
    }

    throw new InvalidArgumentException();
}
