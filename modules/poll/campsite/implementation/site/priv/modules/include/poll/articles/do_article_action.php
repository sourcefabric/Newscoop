<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/settings.ini.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
require_once $_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/modules/include/poll/poll_linker.class.php";

switch ($f_action) {
    case 'delete':
        $moduleHandler =& new poll_linker(); 
        $moduleHandler->linkpoll(null, 'article', $articleObj->getLanguageId(), null, null, null, $articleObj->getArticleNumber());
        unset($moduleHandler);
    break;
}
?>