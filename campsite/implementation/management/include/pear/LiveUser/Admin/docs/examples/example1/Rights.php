<?php
require_once 'index.php';
echo '<h3>Rights</h3>';

$areas = $admin->perm->getAreas();
if ($areas === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif  (empty($areas)) {
    echo 'Run the <strong>Area</strong> test first<br />';
    exit;
}

// Add
foreach ($areas as $row) {
    for ($i = 1; $i < 20; $i++) {
        $data = array(
            'area_id' => $row['area_id'],
            'right_define_name' => 'RIGHT_' . $row['area_id'] . '_' . rand(),
        );
        $rightId = $admin->perm->addRight($data);
        if ($rightId === false) {
            echo '<strong>Error on line: '.__LINE__.'</strong><br />';
            print_r($admin->getErrors());
        } else {
            echo 'Created Right Id <strong>'.$rightId.'</strong><br />';
        }
    }
}

// Get
$rights = $admin->perm->getRights();

if ($rights === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($rights)) {
    echo 'No rights were found, thus we\'ve halted the rest of the test<br />';
} else {
    echo 'These are our current rights:';
    Var_Dump::display($rights);
    echo '<br />';

    // Remove
    $id = array_rand($rights);
    $filters = array('right_id' => $rights[$id]['right_id']);
    $rmRight = $admin->perm->removeRight($filters);

    if ($rmRight === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>Right_' . $id . '</strong> was removed<br />';
    }

    // Update
    $id = array_rand($rights);
    $data = array('right_define_name' => 'RIGHT_' . $id . '_UPDATED');
    $filters = array('right_id' => $rights[$id]['right_id']);
    $upRight = $admin->perm->updateRight($data, $filters);

    if ($upRight === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>Right_'. $id .'</strong> was updated<br />';
        $params = array('filters' => array('right_id' => $rights[$id]['right_id']));
        $result = $admin->perm->getRights($params);

        if ($result === false) {
            echo '<strong>Error on line: '.__LINE__.'</strong><br />';
            print_r($admin->getErrors());
        } elseif (empty($result)) {
            echo 'No rights were found<br />';
        } else {
            Var_Dump::display($result);
        }
    }

    // Get
    $rights = $admin->perm->getRights();

    if ($rights === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($rights)) {
        echo 'No rights were found<br />';
    } else {
        echo 'These are our current rights:';
        Var_Dump::display($rights);
        echo '<br />';
    }
}
echo '<hr />';
