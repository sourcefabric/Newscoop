<?php
require($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files($ADMIN_DIR);
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/login.php");
	return;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<TITLE><?php putGS("CAMPSITE"); ?></TITLE>
</HEAD>
<FRAMESET ROWS="75,*" BORDER="0">
    <FRAME SRC="menu.php" NAME="fmenu" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="AUTO">
    <FRAME SRC="home.php" NAME="fmain" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="AUTO">
</FRAMESET>
</HTML>
