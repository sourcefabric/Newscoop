<?php
require_once 'index.php';
echo '<h3>Group</h3>';

// Add
for ($i = 1; $i < 20; $i++) {
    $data = array('group_define_name' => 'GROUP'.rand());
    $groupId = $admin->perm->addGroup($data);

    if ($groupId === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'Created Group Id <strong>'.$groupId.'</strong><br />';
    }
}

// Get
echo 'All the groups:<br />';
$groups = $admin->perm->getGroups();

if ($groups === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($groups)) {
    echo 'No groups were found, thus we\'ve halted the rest of the test<br />';
} else {
    Var_Dump::display($groups);
    echo '<br />';

    // Remove
    $id = array_rand($groups);
    $filters = array('group_id' => $groups[$id]['group_id']);
    $removed = $admin->perm->removeGroup($filters);

    if ($removed === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>' . $groups[$id]['group_id'] . '</strong> was deleted<br />';
        unset($groups[$id]);
    }

    // test recursive
    $id = array_rand($groups);
    $filters = array(
        'group_id' => $groups[$id]['group_id'],
        'recursive' => true
     );
    $removed = $admin->perm->removeGroup($filters);

    if ($removed === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>' . $groups[$id]['group_id'] . '</strong> was deleted and all its child groups<br />';
        unset($groups[$id]);
    }

    // Update
    $id = array_rand($groups);
    $filters = array('group_id' => $groups[$id]['group_id']);
    $data = array('group_define_name' => 'GROUP_' . $groups[$id]['group_id'] . '_UPDATED');
    $updated = $admin->perm->updateGroup($data, $filters);

    if ($updated === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>' . $groups[$id]['group_id'] . '</strong> was updated<br />';
        $params = array('filters' => array('group_id' => $groups[$id]['group_id']));
        $group = $admin->perm->getGroups($params);

        if ($group === false) {
            echo '<strong>Error on line: '.__LINE__.'</strong><br />';
            print_r($admin->getErrors());
        } elseif (empty($group)) {
            echo 'No groups were found<br />';
        } else {
            Var_Dump::display($group);
            echo '<br />';
        }
    }

    // Get
    echo 'All the groups:<br />';

    $groups = $admin->perm->getGroups();
    if ($groups === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($groups)) {
        echo 'No groups were found<br />';
    } else {
        Var_Dump::display($groups);
        echo '<br />';
    }
}
echo '<hr />';
