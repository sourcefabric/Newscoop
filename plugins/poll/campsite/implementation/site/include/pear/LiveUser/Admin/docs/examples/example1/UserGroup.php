<?php
require_once 'index.php';
echo '<h3>UserGroup</h3>';

$groups = $admin->perm->getGroups();
if ($groups === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
    exit;
} elseif  (empty($groups)) {
    echo 'Run the <b>Group</b> test first<br />';
    exit;
}

$users = $admin->getUsers(array('container' => 'auth'));
if ($users === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
    exit;
} elseif  (empty($users)) {
    echo 'Run the <b>User</b> test first<br />';
    exit;
}
// Add

foreach ($groups as $group) {
    $rand = array_rand($users);
    $result = $admin->perm->addUserToGroup(array('perm_user_id' => $users[$rand]['perm_user_id'], 'group_id' => $group['group_id']));

    if ($result === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<b>' . $users[$rand]['name'] . '</b> was added to group <b>' . $group['group_id'] . '</b><br />';
    }
}
// Get users from one group
$group = array_rand($groups);

$params = array(
    'filters' => array(
        'group_id' => $groups[$group]['group_id']
    )
);
$usersGroup = $admin->perm->getUsers($params);

if ($usersGroup === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($usersGroup)) {
    echo 'No users were found for the group id <strong>' . $groups[$group]['group_id'] . '</strong>';
} else {
    echo 'Perm ID\'s of the users in group <b>' . $groups[$group]['group_id'] . '</b><br />';
    Var_Dump::display($usersGroup);
    echo '<br />';
}

// Remove user from one group
$group = array_rand($groups);
$user = array_rand($users);

$filters = array(
    'group_id' => $groups[$group]['group_id'],
    'perm_user_id' => $users[$user]['perm_user_id']
);
$removed = $admin->perm->removeUserFromGroup($filters);

if ($removed === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} else {
    echo '<b>' . $users[$user]['name'] . '</b> was removed from group <b>'.$groups[$group]['group_id'].'</b><br />';
}

// Remove user from all his groups
$user = array_rand($users);
$filters = array(
    'perm_user_id' => $users[$user]['perm_user_id']
);
$removed = $admin->perm->removeUserFromGroup($filters);

if ($removed === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} else {
    echo '<b>' . $users[$user]['name'] . '</b> was removed from <b>ALL</b> his groups<br />';
}

// Get users from all groups
foreach ($groups as $group) {
    $params = array(
        'filters' => array(
            'group_id' => $group['group_id']
        )
    );
    $usersGroup = $admin->perm->getGroups($params);

    if ($usersGroup === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($usersGroup)) {
        echo 'No users were found for the group id <strong>' . $group['group_id'] . '</strong>';
    } else {
        echo 'Perm ID\'s of the users in group <b>' . $group['group_id'] . '</b><br />';
        Var_Dump::display($usersGroup);
        echo '<br />';
    }
}
echo '<hr />';
