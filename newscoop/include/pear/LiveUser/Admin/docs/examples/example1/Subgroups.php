<?php
require_once 'index.php';
echo '<h3>Subgroups</h3>';

$groups = $admin->perm->getGroups();
if ($groups === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif  (empty($groups)) {
    echo 'Run the <strong>Group</strong> test first<br />';
    exit;
}

$groups_with_subgroup = array();
for ($i = 0; $i < 10; $i++) {
    $group = array_rand($groups);
    $subgroup = array_rand($groups);
    $groups_with_subgroup[] = $groups[$group]['group_id'];

    if ($group === $subgroup) {
        continue;
    }

    $data = array(
        'group_id' => $groups[$group]['group_id'],
        'subgroup_id' => $groups[$subgroup]['group_id']
    );
    $assign = $admin->perm->assignSubGroup($data);

    if ($assign === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>' . $groups[$subgroup]['group_id'] . '</strong> is now
              subgroup of <strong>'. $groups[$group]['group_id'] .'</strong><br />';
    }
}

    echo '<br /><br />All the groups with hierarchy mode on and rekey to true:<br />';
    $groups = $admin->perm->getGroups(
        array(
            'select' => 'all',
            'rekey' => true,
            'filters' => array('group_id' => $groups_with_subgroup),
            'hierarchy' => true,
        )
    );
    if ($groups === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($groups)) {
        echo 'No groups were found<br />';
    } else {
        Var_Dump::display($groups);
        echo '<br />';
    }


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

    // unassignSugroup
    // By group id
    $id = array_rand($groups);
    $filters = array('group_id' => $groups[$id]['group_id']);

    echo 'Group with subgroups: '.$groups[$id]['group_id'].'<br />';
    $subgroups = $admin->perm->getGroups(array('subgroups' => true, 'filters' => $filters));
    if ($subgroups === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        Var_Dump::display($subgroups);
        echo '<br />';
    }

    $unassign = $admin->perm->unassignSubGroup($filters);

    if ($unassign === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'Removed all records with the group id <strong>' . $groups[$id]['group_id'] . '</strong><br />';
        unset($groups[$id]);
    }

    // By subgroup id
    $id = array_rand($groups);
    $filters = array('subgroup_id' => $groups[$id]['group_id']);
    $unassign = $admin->perm->unassignSubGroup($filters);

    if ($unassign === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'Removed all records with the subgroup id <strong>' . $groups[$id]['group_id'] . '</strong><br />';
        unset($groups[$id]);
    }
    // By subgroup id and group id
    $group = array_rand($groups);
    $subgroup = array_rand($groups);
    $filters = array(
        'group_id' => $groups[$group]['group_id'],
        'subgroup_id' => $groups[$subgroup]['group_id']
    );
    $unassign = $admin->perm->unassignSubGroup($filters);

    if ($unassign === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
    echo 'Removed the record that has <strong>' . $groups[$group]['group_id'] . '</strong>
              as group id  and <strong>' . $groups[$subgroup]['group_id'] . '</strong> as subgroup id<br />';
    }

    echo '<br /><br />Test getParentGroup:<br />';
    for ($i = 0; $i < 5; $i++) {
        $subgroup = array_rand($groups);
        $result = $admin->perm->getParentGroup($groups[$subgroup]['group_id']);
        if ($result === false) {
            echo '<strong>Error on line: '.__LINE__.'</strong><br />';
            print_r($admin->getErrors());
        } else {
            echo 'Group <strong>' . $result['group_id'] . '</strong> is the parent group of <strong>' . $groups[$subgroup]['group_id'] . '</strong><br />';
        }
    }

    // Get
    echo '<br /><br />All the groups:<br />';
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