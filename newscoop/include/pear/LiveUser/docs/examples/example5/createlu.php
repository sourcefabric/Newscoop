<?php

PEAR::setErrorHandling(PEAR_ERROR_RETURN);

$usr = LiveUser::singleton($conf);

if (!$usr->init()) {
    var_dump($usr->getErrors());
    die();
}

$handle = (array_key_exists('handle', $_REQUEST)) ? $_REQUEST['handle'] : null;
$passwd = (array_key_exists('passwd', $_REQUEST)) ? $_REQUEST['passwd'] : null;
$logout = (array_key_exists('logout', $_REQUEST)) ? $_REQUEST['logout'] : false;
if ($logout) {
    $usr->logout(true);
} elseif(!$usr->isLoggedIn() || ($handle && $usr->getProperty('handle') != $handle)) {
    if (!$handle) {
        $usr->login(null, null, true);
    } else {
        $usr->login($handle, $passwd);
    }
}

?>