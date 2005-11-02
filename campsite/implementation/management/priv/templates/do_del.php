<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/templates/template_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('DeleteTempl')) {
	camp_html_display_error(getGS("You do not have the right to delete templates."));
	exit;
}

$Path = Input::Get('Path', 'string', '');
if (!Template::IsValidPath($Path)) {
	header("Location: /$ADMIN/templates/");
	exit;
}
$Name = Input::Get('Name', 'string', '');
$What = Input::Get('What', 'int', 0);

$dir = urldecode($Path)."/".urldecode($Name);
$fileFullPath = Template::GetFullPath(urldecode($Path), $Name);
$errorMsgs = array();

$errorReportingState = error_reporting(0);
$deleted = false;
if ($What == '0') {
	$deleted = rmdir($fileFullPath);
	if (!$deleted) {
		$errorMsgs[] = getGS("The folder could not be deleted.").' '.getGS("The directory must be empty");
	}
} else {
	$template_path = Template::GetPath($Path, $Name);
	if (!Template::InUse($template_path)) {
		$deleted = unlink($fileFullPath);
		if ($deleted) {
			$logtext = getGS('Template $1 was deleted', mysql_real_escape_string($dir));
			Log::Message($logtext, $User->getUserName(), 112);
			Template::UpdateStatus();
		}
		else {
			$errorMsgs[] = getGS("The template could not be deleted.");
		}
	} else {
		$errorMsgs[] = getGS("The template $1 is in use and can not be deleted.", $fileFullPath);
	}
}
error_reporting($errorReportingState);

if ($deleted) {
	header("Location: /$ADMIN/templates/?Path=" . urlencode($Path));
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Templates"), "/$ADMIN/templates/");
$crumbs = array_merge($crumbs, camp_template_path_crumbs($path));
if ($What == 1) { 
	$crumbs[] = array(getGS("Deleting template"), ""); 
} else { 
	$crumbs[] = array(getGS("Deleting folder"), ""); 
}
echo camp_html_breadcrumbs($crumbs);

?>
<P>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php if($What == 1) { putGS("Deleting template"); } else { putGS("Deleting folder"); } ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php 
			foreach ($errorMsgs as $errorMsg) { ?>
				<li><?php p($errorMsg); ?></li>
				<?php
			}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/templates?Path=".urlencode($Path); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
