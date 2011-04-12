<?php
require_once 'index.php';
echo '<h3>UserRights</h3>';

$users = $admin->getUsers(array('container' => 'auth'));
if ($users === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
    exit;
} elseif (empty($users)) {
    echo 'Run the <strong>User</strong> test first<br />';
    exit;
}

$rights = $admin->perm->getRights();
if ($rights === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
    exit;
} elseif (empty($rights)) {
    echo 'Run the <strong>Right</strong> test first<br />';
    exit;
}

for ($i = 1; $i < 30; $i++) {
    $user = array_rand($users);
    $right = array_rand($rights);
    $data = array(
        'perm_user_id' => $users[$user]['perm_user_id'],
        'right_id' => $rights[$right]['right_id'],
        'right_level' => 1,
    );
    $granted = $admin->perm->grantUserRight($data);

    if ($granted === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>' . $users[$user]['name'] . '</strong> was granted the right <strong>' . $rights[$right]['right_id'] . '</strong><br />';
    }
    unset($rights[$right]);
    $rights = array_values($rights);
}

$user = array_rand($users);
$right = array_rand($rights);
$filters = array(
    'perm_user_id' => $users[$user]['auth_user_id'],
    'right_id' => $rights[$right]['right_id']
);
$revoked = $admin->perm->revokeUserRight($filters);

if ($revoked === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} else {
    echo 'The right <strong>' . $rights[$right]['right_id'] . '</strong> has been revoked from <strong>' . $users[$user]['name'] . '</strong><br />';
}

$user = array_rand($users);
$params = array(
    'fields' => array(
        'right_id'
    ),
    'filters' => array(
        'perm_user_id' => $users[$user]['perm_user_id']
    )
);

$user_rights = $admin->perm->getRights($params);

if ($user_rights === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
} elseif (empty($user_rights)) {
    echo 'No rights were found for perm user id <strong>' . $users[$user]['perm_user_id'] . '</strong><br />';
} else {
    $right = array_rand($user_rights);
    $filters = array(
        'perm_user_id' => $users[$user]['auth_user_id'],
        'right_id' => $user_rights[$right]['right_id']
    );
    $data = array('right_level' => 3);

    $update = $admin->perm->updateUserRight($data, $filters);

    if ($update === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'The right <strong>' . $user_rights[$right]['right_id'] . '</strong> has been updated to Level 3 for <strong>' . $users[$user]['name'] . '</strong><br />';
        $params = array(
            'filters' => array(
                'right_id' => $user_rights[$right]['right_id'],
                'perm_user_id' => $users[$user]['perm_user_id']
            )
        );
        $result = $admin->perm->getRights($params);

        if ($result === false) {
            echo '<strong>Error on line: '.__LINE__.'</strong><br />';
            print_r($admin->getErrors());
        } elseif (empty($result)) {
            echo 'No result came from searching for right id <strong>' . $user_rights[$right]['right_id'] . '</strong>
                  and perm user id <strong>' . $users[$user]['perm_user_id'] . '</strong><br />';
        } else {
            Var_Dump::display($result);
        }
    }
}

$user = array_rand($users);
$params = array(
    'fields' => array(
        'right_id',
        'right_level'
    ),
    'filters' => array(
        'perm_user_id' => $users[$user]['perm_user_id']
    )
);
$singleRight = $admin->perm->getRights($params);

if ($singleRight === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($singleRight)) {
    echo 'No rights were found for perm user id <strong>' . $users[$user]['perm_user_id'] . '</strong><br />';
} else {
    echo 'These are the user rights for <strong>' . $users[$user]['perm_user_id'] . '</strong>:<br />';
    Var_Dump::display($singleRight);
    echo '<br />';
}

$params = array(
    'fields' => array(
        'right_id',
        'right_level',
        'perm_user_id',
    ),
    'with' => array(
        'perm_user_id' => array(
            'fields' => array(
                'perm_type',
            ),
        ),
    ),
);

$rights = $admin->perm->getRights($params);
if ($rights === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($rights)) {
    echo 'No rights were found.<br />';
} else {
    echo 'Here are all the rights:<br />';
    Var_Dump::display($rights);
    echo '<br />';
}
echo '<hr />';
