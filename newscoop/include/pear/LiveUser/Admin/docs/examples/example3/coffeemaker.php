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
 * @version $Id: coffeemaker.php,v 1.2 2004/12/18 21:59:05 lsmith Exp $
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
    <title>Example Coffeemaker</title>
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
    <h1>Welcome in the Coffeemaker section!</h1>
    <p>&nbsp;</p>
<?php
// check login status
if (!$LU->isLoggedIn()) {
    $target = 'coffeemaker.php';
    include_once 'loginscreen.php';
    exit();
} else {
    if (!$LU->checkRight(MAKE_COFFEE) && !$LU->checkRight(DRINK_COFFEE)) {
?>
            <p>Sorry, but you don't have any rights in this section</p>
<?php
    } else {
        // Okay, he's in. Now let's display some content dependent of
        // his access rights in our application. Let's pretend the current
        // AuthArea of our application is the Coffeemaker section and we want to
        // know whether to user is allowed to make some coffee
        // (the area is not needed until we won't know whether our user is an area
        // admin).
        if ($LU->checkRight(MAKE_COFFEE)) {
?>
            <p>Congrats, you're in the Coffeemaker section and have
            the right to make coffee!</p>
<?php
        } else {
?>
            <p>Your coffee tastes so bad, I won't allow you to make some</p>
<?php
        }
        // check if the user is allowed to drink coffee. checkRight will return the level.
        if ($LU->checkRight(DRINK_COFFEE) == 1) {
?>
                <p>Yey ! You can even drink some coffee (but perhaps you should ask your boss first ;-) ))</p>
<?php
        } elseif ($LU->checkRight(DRINK_COFFEE) == 3) {
?>
                <p>Hi <?php echo $LU->getProperty('handle'); ?>! Taste this delicious coffee.</p>
<?php
        } else {
?>
                <p>Well, sorry dude, you cannot drink in here</p>
<?php
        }
    }
}
?>
    <p>&nbsp;</p>
    <p class="center"><a href="coffeemaker.php?logout=1">Logout</a></p>
</body>
</html>