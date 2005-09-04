<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");
    
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageTempl')) {
	header("Location: /$ADMIN/ad.php?ADReason=".encURL(getGS("You do not have the right to modify templates.")));
	exit;
}

$cPath = Input::Get('cPath', 'string', '');
if (!Template::IsValidPath($cPath)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$cName = Input::Get('cName', 'string', '');
$Name = Input::Get('Name', 'string', '');

$correct = trim($cName) != "";
if ($correct) {
	$cName = strtr(decS($cName),'?~#%*&|"\'\\/<>', '_____________');
	$newTempl = $Campsite['HTML_DIR']."/look/".decURL($cPath)."/$cName";
	$exists = file_exists($newTempl);
	$ok = 0;
	if (!$exists) {
		$tpl1_name = decURL($cPath)."/$Name";
		$tpl1 = $Campsite['HTML_DIR']."/look/".decURL($cPath)."/$Name";
		$fd = fopen ($tpl1, "r");
		$contents = fread ($fd, filesize ($tpl1));
		fclose ($fd);

		$tpl2_name = decURL($cPath)."/$cName";
		$tpl2 = $Campsite['HTML_DIR']."/look/".decURL($cPath)."/$cName";
		$fd = fopen ($tpl2, "w");
		$res = fwrite ($fd, $contents);
		fclose ($fd);
		$ok = $res == true || strlen($contents) == 0;
		if ($ok) {
			$logtext = getGS('Template $1 was duplicated into $2', $tpl1_name, $tpl2_name);
			query ("INSERT INTO Log SET TStamp=NOW(), IdEvent=115, User='".$User->getUserName()."', Text='$logtext'");
		}
	}
}

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title"><?php  putGS("Duplicate template"); ?></TD>
		<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/<?php echo $ADMIN; ?>/templates/?Path=<?php  pencURL(decS($cPath)); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Templates"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/templates/?Path=<?php  pencURL(decS($cPath)); ?>" ><B><?php  putGS("Templates");  ?></B></A></TD></TR></TABLE></TD>
	</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Path"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pencHTML(decURL($cPath)); ?></TD>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Template"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pencHTML(decURL($Name)); ?></TD>
</TR></TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ALIGN="CENTER" class="table_input">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Duplicate template"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php  
if (!$correct) {
?>		<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
<?php
} else {
	if ($ok) {
		putGS('The template $1 has been created.','<b>'.$cName.'</B>');
	} else {
		putGS('The template $1 could not be created.','<b>'.$cName.'</B>');
	}
}
?>		</BLOCKQUOTE></TD>
	</TR>
<?php  if ($ok) { ?>	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE><LI><?php  putGS('Do you want to edit the template ?'); ?></LI></BLOCKQUOTE></TD>
	</TR>
<?php  } ?>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  if ($ok) { ?>		<INPUT TYPE="button" class="button" NAME="Yes" VALUE="<?php  putGS('Yes'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates/edit_template.php?Path=<?php  pencURL(decS($cPath)); ?>&Name=<?php pencURL($cName); ?>'">
		<INPUT TYPE="button" class="button" NAME="No" VALUE="<?php  putGS('No'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates?Path=<?php  pencURL(decS($cPath)); ?>'">
<?php  } else { ?>
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/templates/dup.php?Path=<?php  pencURL(decS($cPath)); ?>&Name=<?php  pencURL(decS($Name)); ?>'">
<?php  } ?>
		</DIV>
		</TD>
	</TR>
</TABLE>
<P>
<?php
camp_html_copyright_notice();
?>

</HTML>

