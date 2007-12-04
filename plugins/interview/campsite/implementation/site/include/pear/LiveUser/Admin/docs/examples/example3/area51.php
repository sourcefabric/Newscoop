<?php
/**
 * Test for the LiveUser class
 * ===============================
 *
 * This example sets up an authorization system using the LiveUser
 * class. You don't have to use this to use the LiveUser class(es), but
 * this way you don't need to take care of the login-process, storing
 * the user object in a session and more...
 *
 * This example is intended to be used with the DB_Medium Perm driver.
 *
 * @version $Id: area51.php,v 1.2 2004/12/18 21:59:05 lsmith Exp $
 **/

// Include configuration.
require_once 'conf.php';

if (!$LU) {
    die('An unknown error occurred');
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>Example Area51</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <style type="text/css">
    <!--
    table {
        background-color: #CCCCCC;
        border-color: 1px solid #000;
    }
    body {
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 12px;
        color: #000000;
        background-color: #FFFFFF
    }

    .center {
           text-align: center;
    }
    .center table {
           margin: auto;
    }
    -->
    </style>
</head>

<body>
    <h1>Area51</h1>
    <p>&nbsp;</p>
<?php
if (!$LU->isLoggedIn()) {
    $target = 'area51.php';
    include_once 'loginscreen.php';
    exit();
} else {
    if (!$LU->checkRight(ACCESS)) {
?>
        <p>Hey, this is a top secret area. Access denied.</p>
<?php
    } else {
?>
            <p>Live long and prosper, <b><?php echo $LU->getProperty('handle'); ?> </b>.</p>
            <p>You have access at the necessary level <b><?php echo $LU->checkRightLevel(ACCESS, 1, 0); ?></b></p>
            <p>Please wait ... checking some rights ...<br />
<?php
        // you can even check multiple rights with one checkright call
        if ($LU->checkRight(array(LAUNCH_ATOMIC_BOMB, FLY_ALIEN_SPACE_CRAFT))) {
?>
            <p>OK, you're the boss. Let's take our alien space craft, launch the orbital atomic bombs and kick some ass! ;-)<br />
            (Ehm, that was just to test our right system ...)<p>
<?php
        } else {
?>
            <p>Don't touch anything!</p>
<?php
        }
    }
}
?>
    <p>&nbsp;</p>
    <p class="center"><a href="area51.php?logout=1">Logout</a></p>
</body>
</html>