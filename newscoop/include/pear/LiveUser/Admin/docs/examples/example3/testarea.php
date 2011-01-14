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
 * @version $Id: testarea.php,v 1.2 2004/12/18 21:59:05 lsmith Exp $
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
    <title>Example Testarea</title>
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
    <h1>Testarea</h1>
    <p>&nbsp;</p>
<?php
if (!$LU->isLoggedIn()) {
    $target = 'testarea.php';
    include_once 'loginscreen.php';
    exit();
} else {
    if (!$LU->checkRight(READ_TESTS) && !$LU->checkRight(WRITE_TESTS)) {
?>
        <p>Sorry, but you don't have any rights in this section</p>
<?php
    } else {
        if ($LU->checkRight(READ_TESTS)) {
?>
           <p>Wow, you've the right to <b>read</b> the latest posting in this list!<br>
           OK, let's search the database for the last entry ...</p>
           <p>User: <b><?php echo $LU->getProperty('handle'); ?></b><br>LastLogin: <b><?php echo strftime('%Y-%m-%d %H:%M', $LU->getProperty('lastLogin')); ?></b><br>
           <pre>Hi!<br>I'm just testing whether this really cool LiveUser program works :-)<br>
           Hmm, think it runs perfect!</pre>
<?php
        }
    }
}
?>
    <p>&nbsp;</p>
    <p>
<?php
  // let's see if the user is allowed to post something
  if ($LU->checkRight(WRITE_TESTS)) {
     $disabled =  ''; # end the textarea tag
     $text = 'Type something.';
  } else {
     $disabled = 'disabled="disabled"'; # disable the textarea tag
     $text = 'No, you don\'t have the right to post something in this list.';
  }

?>
        <textarea name="textfield" cols="80" rows="10" <?php echo $disabled ?>>
            <?php echo $text; ?>
        </textarea>
    </p>
    <p>&nbsp;</p>
    <p class="center"><a href="testarea.php?logout=1">Logout</a></p>
</body>
</html>