<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
	"http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<?php  include ("./lib_campsite.php");
    $globalfile=selectLanguageFile('.','globals');
    $localfile=selectLanguageFile('.','locals');
    @include ($globalfile);
    @include ($localfile);
    include ("./languages.php");   ?>
<?php  require_once("$DOCUMENT_ROOT/db_connect.php"); ?>


<?php 
    todefnum('TOL_UserId');
    todefnum('TOL_UserKey');
    query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
    $access=($NUM_ROWS != 0);
    if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	 if ($NUM_ROWS){
	 	fetchRow($XPerm);
	 }
	 else $access = 0;						//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	 $xpermrows= $NUM_ROWS;
    }
    else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
    }
?>
    


<HEAD>
    <META http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<TITLE><?php  putGS("CAMPSITE"); ?></TITLE>
<?php  if ($access==0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/priv/login.php">
<?php  } ?></HEAD>

<?php  if ($access) { ?><FRAMESET COLS="150,*" BORDER="0">
    <FRAME SRC="menu.php" NAME="fmenu" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="AUTO">
    <FRAME SRC="home.php" NAME="fmain" FRAMEBORDER="0" MARGINWIDTH="0" SCROLLING="AUTO">
</FRAMESET>
<?php  } ?>

</HTML>
