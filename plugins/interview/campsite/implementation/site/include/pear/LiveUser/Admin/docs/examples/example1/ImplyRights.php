<?php
require_once 'index.php';
echo '<h3>ImplyRights</h3>';

$rights = $admin->perm->getRights();
if  (empty($rights)) {
    echo 'Run the <b>Right</b> test first<br />';
    exit;
}
echo '<hr />';

// add
for ($i = 0; $i < 5; $i++) {
    $right = array_rand($rights);
    $imright = array_rand($rights);
    $data = array(
        'right_id' => $rights[$right]['right_id'],
        'implied_right_id' => $rights[$imright]['right_id']
    );
    $result = $admin->perm->implyRight($data);
    if ($result === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>' . $rights[$imright]['right_id'] . '</strong> is now
              implied right from <strong>'. $rights[$right]['right_id'] .'</strong><br />';
    }
}

$right_id = $rights[$right]['right_id'];
// view all with hierarchy
echo 'All the rights with hierarchy:<br />';
$rights = $admin->perm->getRights(array(
    'hierarchy' => true,
    'rekey' => true,
    'fields' => array('*', 'has_implied'),
    'filters' => array('right_id' => $right_id),
));
if ($rights === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} else {
    Var_Dump::display($rights);
    echo '<br />';
}

// view all with implied and without hierarchy
echo 'All the rights with implied and without hierarchy:<br />';
$rights = $admin->perm->getRights(array(
    'implied' => true,
    'rekey' => true,
    'fields' => array('*', 'has_implied'),
    'filters' => array('right_id' => $right_id),
));
if ($rights === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} else {
    Var_Dump::display($rights);
    echo '<br />';
}

// view all
echo 'All the rights:<br />';
$rights = $admin->perm->getRights();
if ($rights === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($rights)) {
    echo 'No rights were found, thus we\'ve halted the rest of the test<br />';
} else {
    Var_Dump::display($rights);
    echo '<br />';

    // delete
    // By right id
    $id = array_rand($rights);
    $filters = array('right_id' => $rights[$id]['right_id']);
    $unimply = $admin->perm->unimplyRight($filters);

    if ($unimply === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'Removed all records with the right id <strong>' . $rights[$id]['right_id'] . '</strong><br />';
        unset($rights[$id]);
    }

    // By implied right id
    $id = array_rand($rights);
    $filters = array('implied_right_id' => $rights[$id]['right_id']);
    $unimply = $admin->perm->unimplyRight($filters);

    if ($unimply === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'Removed all records with the implied right id <strong>' . $rights[$id]['right_id'] . '</strong><br />';
        unset($rights[$id]);
    }

    // By implied right id and right id
    $right = array_rand($rights);
    $impliedRight = array_rand($rights);

    $filters = array(
        'right_id' => $rights[$right]['right_id'],
        'implied_right_id' => $rights[$impliedRight]['right_id']
    );
    $unimply = $admin->perm->unimplyRight($filters);

    if ($unimply === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'Removed the record that has <strong>' . $rights[$right]['right_id'] . '</strong>
              as right id  and <strong>' . $rights[$impliedRight]['right_id'] . '</strong> as implied right id<br />';
    }

    // view all
    echo 'All the rights:<br />';
    $rights = $admin->perm->getRights();
    if ($rights === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($rights)) {
        echo 'No rights were found<br />';
    } else {
        Var_Dump::display($rights);
        echo '<br />';
    }
}
echo '<hr />';