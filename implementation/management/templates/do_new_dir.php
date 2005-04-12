<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");
    
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTempl')) {
	header("Location: /$ADMIN/ad.php?ADReason=".encURL(getGS("You do not have the right to create new folders.")));
	exit;
}

$cPath = Input::Get('cPath', 'string', '');
if (!Template::IsValidPath($cPath)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$cName = Input::Get('cName', 'string', '');
$correct = trim($cName) != "";
if ($correct) {
	$cName = strtr(decS($cName), '?~#%*&|"\'\\/<>', '_____________');
	$newdir = $Campsite['HTML_DIR'] . "/look/" . decURL($cPath) . "/$cName";
	$ok = 0;
	$file_exists = file_exists($newdir);
	if (!$file_exists) {
		$dir = mkdir($newdir, 0755);
		$ok = $dir === true;
	}
}

if ($ok)
	header("Location: /$ADMIN/templates?Path=" . encURL("$cPath/$cName"));
?>

<BODY>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Creating new folder"); ?>
		</TD>
		<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/<?php echo $ADMIN; ?>/templates/?Path=<?php  pencURL(decS($cPath)); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php putGS("Templates"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/templates/?Path=<?php  pencURL(decS($cPath)); ?>" ><B><?php putGS("Templates");  ?></B></A></TD></TR></TABLE></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR><TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Path"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pencHTML(decURL($cPath)); ?></TD></TR>
</TABLE>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ALIGN="CENTER" class="table_input">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Creating new folder"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php  
if (!$correct) {
?>	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php
} else {
	if ($ok) {
		putGS('The folder $1 has been created','<b>'.$cName.'</B>');
	} else {
		if ($file_exists)
			putGS('A file or folder having the name $1 already exists','<b>'.$cName.'</B>');
		else
			putGS('The folder $1 could not be created','<b>'.$cName.'</B>');
	}
}
?>	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  if ($ok) { ?>		<INPUT TYPE="button" class="button" NAME="New" VALUE="<?php  putGS('Add another'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates/new_dir.php?Path=<?php  pencURL(decS($cPath)); ?>'">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".encHTML(decS("$cPath/$cName")); ?>'">
<?php  } else { ?>
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".encHTML(decS($cPath)); ?>'">
<?php  } ?>		</DIV>
		</TD>
	</TR>
</TABLE>
<P>
<?php
CampsiteInterface::CopyrightNotice();
?>

</HTML>
