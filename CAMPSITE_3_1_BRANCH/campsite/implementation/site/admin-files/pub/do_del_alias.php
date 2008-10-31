<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Alias = Input::Get('Alias', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$aliasObj = new Alias($Alias);
$errorMsgs = array();

if ($publicationObj->getDefaultAliasId() != $Alias) {
	$deleted = $aliasObj->delete();

	if ($deleted) {
		$logtext = getGS('The alias $1 has been deleted from publication $2.',
						 $aliasObj->getName(), $publicationObj->getName());
		Log::Message($logtext, $g_user->getUserName(), 152);
		camp_html_goto_page("/$ADMIN/pub/aliases.php?Pub=$Pub");
	} else {
		$errorMsgs[] = getGS('The alias $1 could not be deleted.','<B>'.$aliasObj->getName().'</B>');
	}
} else {
	$errorMsgs[] = getGS('$1 is the default publication alias, it can not be deleted.', '<B>'.$aliasObj->getName().'</B>');
}

$crumbs = array(getGS("Publication Aliases") => "aliases.php?Pub=$Pub");
camp_html_content_top(getGS("Deleting alias"), array("Pub" => $publicationObj), true, false, $crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Deleting alias"); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/aliases.php?Pub=<?php p($Pub); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
