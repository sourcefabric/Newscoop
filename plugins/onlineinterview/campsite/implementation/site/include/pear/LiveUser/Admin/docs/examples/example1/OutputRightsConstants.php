<?php require_once 'index.php'; ?>
<h3>outputRightsConstants</h3>
<?php
$rights = $admin->perm->outputRightsConstants('array', array('naming' => LIVEUSER_SECTION_APPLICATION), 'php');

Var_Dump::display($rights);
