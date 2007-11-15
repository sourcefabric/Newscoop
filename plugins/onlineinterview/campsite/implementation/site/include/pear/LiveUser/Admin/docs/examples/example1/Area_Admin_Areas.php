<?php
require_once 'index.php';
echo '<h3>Area Admin Areas</h3>';

$applications = $admin->perm->getApplications();
if ($applications === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif  (empty($applications)) {
    echo 'Run the <strong>Application</strong> test first<br />';
    exit;
}

$areas = $admin->perm->getAreas();
if ($areas === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($areas)) {
    echo 'Run the <strong>Areas</strong> test first<br />';
    exit;
}

$users = $admin->getUsers(array('filters' => array('perm_type' => '3')));
if ($users === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($users)) {
    echo 'Please run the <a href="User.php'.$qstring.'">User</a> file to be able to test the area admin part.<br />';
    exit;
}

// Add
for ($i = 0; $i < 15; $i++) {
    $id = array_rand($areas);
    $uid = array_rand($users);

    $data = array(
        'area_id' => $areas[$id]['area_id'],
        'perm_user_id' => $users[$uid]['perm_user_id']
    );

    $result = $admin->perm->addAreaAdmin($data);
    if ($result === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'perm user id <strong>' . $users[$uid]['perm_user_id'] . '</strong> was added as admin over area id <strong>' . $areas[$id]['area_id'] . '</strong><br />';
    }
}

// Remove
// By area id
$id = array_rand($areas);
$filters = array(
    'area_id' => $areas[$id]['area_id']
);

$filter = array('filters' => $filters);
$result = $admin->perm->getAreas($filter);
echo '<br /><br />The area admins we\'re about to remove<br />';
Var_Dump::display($result);

$result = $admin->perm->removeAreaAdmin($filters);

if ($result === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} else {
   echo 'Removed area admin with area id <strong>' . $areas[$id]['area_id'] . '</strong><br />';
}

unset($areas[$id]);

// by right id
$uid = array_rand($users);
$filters = array(
    'perm_user_id' => $users[$uid]['perm_user_id'],
);

$filter = array('filters' => $filters);
$result = $admin->perm->getAreas($filter);
echo '<br /><br />The areas that the admin was over and we\'re about to remove<br />';
Var_Dump::display($result);

$result = $admin->perm->removeAreaAdmin($filters);

if ($result === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} else {
    echo 'Removed area admin with perm user id <strong>' . $users[$uid]['perm_user_id'] . '</strong><br />';
}

unset($users[$uid]);

// by area and right id
$id = array_rand($areas);
$uid = array_rand($users);
$filters = array(
    'perm_user_id' => $users[$uid]['perm_user_id'],
    'area_id' => $areas[$id]['area_id']
);

$filter = array('filters' => $filters);
$result = $admin->perm->getAreas($filter);
echo '<br /><br />The area admin we\'re about to remove<br />';
Var_Dump::display($result);

$result = $admin->perm->removeAreaAdmin($filters);
if ($result === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} else {
    echo 'Removed area admin with perm user id <strong>' . $users[$uid]['perm_user_id'] . '</strong>
         and area id <strong>' . $areas[$id]['area_id'] . '</strong><br />';
}

unset($areas[$id]);
unset($users[$uid]);
?>