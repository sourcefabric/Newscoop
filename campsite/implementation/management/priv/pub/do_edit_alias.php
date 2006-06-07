<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$cPub = Input::Get('cPub', 'int');
$cAlias = Input::Get('cAlias', 'int');
$cName = trim(Input::Get('cName'));
$publicationObj =& new Publication($cPub);

$correct = true;
$updated = false;
$errorMsgs = array();

if (empty($cName)) {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.', '<B>Name</B>');
}

$aliases = 0;
if ($correct) {
	$aliasDups = count(Alias::GetAliases(null, null, $cName));
	if ($aliasDups <= 0) {
		$alias =& new Alias($cAlias);
		$alias->setName($cName);
		$updated = true;
		$logtext = getGS('The site alias for publication $1 has been modified to $2.',
						 $publicationObj->getName(), $cName);
		Log::Message($logtext, $g_user->getUserName(), 153);
		header("Location: /$ADMIN/pub/edit_alias.php?Pub=$cPub&Alias=$cAlias");
		exit;
	}
	else {
		$errorMsgs[] = getGS('Another alias with the same name exists already.');
	}
}

if (!$updated && !$correct) {
	$errorMsgs[] = getGS('The site alias $1 could not be modified.', '<B>'.$cName.'</B>');
}

$crumbs = array(getGS("Publication Aliases") => "aliases.php?Pub=$cPub");
camp_html_content_top(getGS("Editing alias"), array("Pub" => $publicationObj), true, false, $crumbs);

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Editing alias"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?PHP
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php echo $errorMsg; ?></li>
			<?PHP
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/edit_alias.php?Pub=<?php p($cPub); ?>&Alias=<?php p($cAlias); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
