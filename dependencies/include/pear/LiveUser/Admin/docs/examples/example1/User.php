<?php
require_once 'index.php';
echo '<h3>User</h3>';

// Add
echo 'Make 10 normal users and 10 admins<br />';
for ($i = 1; $i < 21; $i++) {
    $data = array(
        'handle' => 'johndoe' . rand(),
        'passwd' => 'dummypass',
        'name'  => 'asdf'.$i,
        'email' => 'fleh@example.com'.$i
    );

    if ($i > 10) {
        $data['perm_type'] = 3;
    } else {
        $data['perm_type'] = 1;
    }

    $user_id = $admin->addUser($data);
    if ($user_id === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'Created User Id <strong>' . $user_id . '</strong><br />';
    }
}

// Get
// Group of users
echo 'All the users:<br />';
$users = $admin->getUsers(array('container' => 'auth'));
if ($users === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($users)) {
    echo 'No users were found, thus we\'ve halted the rest of the test<br />';
} else {
    Var_Dump::display($users);
    echo '<br />';

    $id = array_rand($users);
    // single user
    echo 'This user will be removed:<br />';
    $user = $admin->getUsers(array('filters' => array('perm_user_id' => $users[$id]['perm_user_id'])));
    if ($user === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($user)) {
        echo 'No user was found.<br />';
    } else {
        Var_Dump::display($user);
        echo '<br />';
    }

    // Remove
    $removed = $admin->removeUser($users[$id]['perm_user_id']);

    if ($removed === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>' . $id . '</strong> was deleted<br />';
        unset($users[$id]);
    }

    // Update
    $id = array_rand($users);
    $updateUser = $users[$id]['perm_user_id'];
    $data = array(
        'handle' => 'updated_user'.rand(),
        'passwd' => 'foo',
    );
    $updated = $admin->updateUser($data, $updateUser);
    if ($updated === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>' . $updateUser . '</strong> was updated<br />';
        $user = $admin->getUsers(array('filters' => array('perm_user_id' => $updateUser)));

        if ($user === false) {
            echo '<strong>Error on line: '.__LINE__.'</strong><br />';
            print_r($admin->getErrors());
        } elseif (empty($user)) {
            echo 'No user was found.<br />';
        } else {
            Var_Dump::display($user);
            echo '<br />';
        }
    }

    // Get
    echo 'All the users:<br />';

    $users = $admin->getUsers(array('container' => 'auth'));
    if ($users === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($users)) {
        echo 'No users were found.<br />';
    } else {
        Var_Dump::display($users);
        echo '<br />';
    }

    $user = array_rand($users);

    echo 'Test fetching auth_user_id AND perm_user_id with PERM getUsers()<br />';
    echo 'Auth<br />';
    $params = array('filters' => array('auth_user_id' => $users[$user]['auth_user_id']));
    $user = $admin->auth->getUsers($params);
    if ($user === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($user)) {
        echo 'No user was found.';
    } else {
        Var_Dump::display($user);
        echo '<br />';
    }
    unset($user);

    echo 'Perm<br />';
    $params = array('filters' => array('perm_user_id' => '3'));
    $user = $admin->perm->getUsers($params);
    if ($user === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($user)) {
        echo 'No user was found.<br />';
    } else {
        Var_Dump::display($user);
        echo '<br />';
    }
}
echo '<hr />';
