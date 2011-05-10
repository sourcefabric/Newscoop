<?php

$auth = Zend_Auth::getInstance();
if ($auth->hasIdentity()) {
    Article::UnlockByUser((int) $auth->getIdentity());
    $auth->clearIdentity();
}

camp_html_goto_page("/$ADMIN/login.php");
