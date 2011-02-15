<?php
require_once 'index.php';
echo '<h3>Application</h3>';

// Add
for ($i = 1; $i < 4; $i++) {
    $data = array('application_define_name' => 'APP'.rand());
    $appId = $admin->perm->addApplication($data);

    if ($appId === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo 'Created Application id <strong>' . $appId . '</strong><br />';
    }
}

// Get
$applications = $admin->perm->getApplications();

if ($applications === false) {
    echo '<strong>Error on line: '.__LINE__.'</strong><br />';
    print_r($admin->getErrors());
} elseif (empty($applications)) {
    echo 'No applications were found, thus we\'ve halted the rest of the test<br />';
} else {
    echo 'These are our current applications:';
    Var_Dump::display($applications);
    echo '<br />';

    // Remove
    $id = array_rand($applications);
    $filters = array('application_id' => $applications[$id]['application_id']);
    $removeApp = $admin->perm->removeApplication($filters);

    if ($removeApp === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>App3</strong> was removed<br />';
        unset($applications[$id]);
    }

    // Update
    $id = array_rand($applications);
    $data = array('application_define_name' => 'APP2_' . $applications[$id]['application_id'] . 'updated');
    $filters = array('application_id' => $applications[$id]['application_id']);
    $updateApp = $admin->perm->updateApplication($data, $filters);

    if ($updateApp === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } else {
        echo '<strong>App2</strong> was updated<br />';
        $params = array('filters' => array('application_id' => $applications[$id]['application_id']));
        $result = $admin->perm->getApplications($params);

        if ($result === false) {
            echo '<strong>Error on line: '.__LINE__.'</strong><br />';
            print_r($admin->getErrors());
        } elseif (empty($result)) {
            echo 'No applications were found<br />';
        } else {
            Var_Dump::display($result);
        }
    }

    // Get
    $applications = $admin->perm->getApplications();

    if ($applications === false) {
        echo '<strong>Error on line: '.__LINE__.'</strong><br />';
        print_r($admin->getErrors());
    } elseif (empty($applications)) {
        echo 'No applications were found<br />';
    } else {
        echo 'These are our current applications:';
        Var_Dump::display($applications);
        echo '<br />';
    }
}

echo '<hr />';
