<?php
require_once 'index.php';
echo '<h3>GroupRights</h3>';

$groups = $admin->perm->getGroups();
if ($groups === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif  (empty($groups)) {
    echo 'Run the <strong>Group</strong> test first<br />';
    exit;
}

$rights = $admin->perm->getRights();
if ($rights === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif  (empty($rights)) {
    echo 'Run the <strong>Right</strong> test first<br />';
    exit;
}


for ($i = 0; $i < 20; $i++) {
    $right   = array_rand($rights);
    $group = array_rand($groups);
    $data = array(
        'group_id' => $groups[$group]['group_id'],
        'right_id' => $rights[$right]['right_id']
    );
    $granted = $admin->perm->grantGroupRight($data);

    if ($granted === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    } else {
        echo 'Group <strong>' . $groups[$group]['group_id'] . '</strong> was granted the right <strong>'.$rights[$right]['right_id'].'</strong><br />';
    }
    unset($rights[$right]);
    $rights = array_values($rights);
}

$group = array_rand($groups);
$params = array(
    'fields' => array(
        'right_id',
        'right_define_name',
        'group_id'
    ),
    'with' => array(
        'group_id' => array(
            'fields' => array(
                'group_id'
            ),
        ),
    ),
    'filters' => array(
        'group_id' => $groups[$group]['group_id']
    ),
    'by_group' => true,
    'limit' => 10,
    'offset' => 0,
);
$allGroupRights = $admin->perm->getRights($params);

if ($allGroupRights === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
} elseif (empty($allGroupRights)) {
    echo 'Group <stong>' . $groups[$group]['group_id'] . '</strong> had no rights<br />';
} else {
    echo '<hr />Here is/are <strong>' . count($allGroupRights) . '</strong> group right(s) for the group <strong>' . $groups[$group]['group_id'] . '</strong>:<br />';
    Var_Dump::display($allGroupRights);
    echo '<br />';
}

$right   = array_rand($rights);
$group = array_rand($groups);
$filters = array(
    'right_id' => $rights[$right]['right_id'],
    'group_id' => $groups[$group]['group_id']
);
$removed = $admin->perm->revokeGroupRight($filters);

if ($removed === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} else {
    echo 'Removed the right <strong>'.$right.'</strong> on group <strong>'.$group.'</strong><br />';
}


$group = array_rand($groups);
$params = array(
    'fields' => array(
        'right_id'
    ),
    'filters' => array(
        'group_id' => $groups[$group]['group_id']
    ),
    'by_group' => true,
);
$rights_group = $admin->perm->getRights($params);
if ($rights_group === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($rights_group)) {
    echo 'Group <strong>' . $groups[$group]['group_id'] . '</strong> had no rights<br />';
} else {
    $right = array_rand($rights_group);
    $data = array('right_level' => 2);
    $filters = array(
        'right_id' => $rights_group[$right]['right_id'],
        'group_id' => $groups[$group]['group_id']
    );
    $updated = $admin->perm->updateGroupRight($data, $filters);

    if ($updated === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'Updated the right level of <strong>' . $groups[$group]['group_id'] . '</strong><br />';
        $params = array(
            'fields' => array(
                'right_id'
            ),
            'filters' => array(
                'right_id' => $rights_group[$right]['right_id'],
                'group_id' => $groups[$group]['group_id']
            ),
            'by_group' => true,
        );
        $result = $admin->perm->getRights($params);

        if ($result === false) {
            echo '<strong>Error on line: '.__LINE__.'</strong><br />';
            print_r($admin->getErrors());
        } elseif (empty($result)) {
            echo 'Nothing was found with the right id <strong>' . $rights_group[$right]['right_id'] . '</strong>
                  and group id <strong>' . $groups[$group]['group_id']. '</strong><br />';
        } else {
            Var_Dump::display($result);
        }
    }
}

$params = array(
    'fields' => array(
        'right_id',
        'group_id',
    ),
    'with' => array(
        'group_id' => array(
            'fields' => array(
                'group_id',
                'right_level',
            )
        ),
    ),
    'by_group' => true,
);

$allGroups = $admin->perm->getRights($params);
echo 'Here are all the group rights after the changes:<br />';
if ($allGroups === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($allGroups)) {
    echo 'Found no groups<br />';
} else {
    Var_Dump::display($allGroups);
}
echo '<hr />';
