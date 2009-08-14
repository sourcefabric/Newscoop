<?php
/**
 * @author $Author: paul $
 * @version $Revision: 2774 $
 */
$login = Alib::GetSessLogin($_REQUEST['alibsid']);
if(!isset($login)||$login==''){
    $_SESSION['alertMsg'] = "Login required";
    header("Location: alibExLogin.php");
    exit;
}

?>