<?php

require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$cPub = Input::Get('cPub', 'int');
$cName = trim(Input::Get('cName'));

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($cPub);
$backLink = "/$ADMIN/pub/add_alias.php?Pub=$cPub";

$correct = true;
$created = false;
$errorMsgs = array();
if (empty($cName)) {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.', '<B>Name</B>');
}

$aliases = 0;
if ($correct) {
	$aliasDups = count(Alias::GetAliases(null, null, $cName));
	if ($aliasDups <= 0) {
		$newAlias = new Alias();
		$created = $newAlias->create(array('Name' => "$cName", "IdPublication" => "$cPub"));
		if ($created) {
			$logtext = getGS('The site alias $1 has been added to publication $2.',
						$cName, $publicationObj->getName());
			Log::Message($logtext, $g_user->getUserId(), 151);
			camp_html_goto_page("/$ADMIN/pub/aliases.php?Pub=$cPub");
		}
	}
	else {
		$errorMsgs[] = getGS('Another alias with the same name exists already.');
	}
}

if (!$created && !$correct) {
	$errorMsgs[] = getGS('The site alias $1 could not be added.', '<B>'.$cName.'</B>');
}

$crumbs = array(getGS("Publication Aliases") => "aliases.php?Pub=$cPub");
camp_html_content_top(getGS("Adding new alias"), array("Pub" => $publicationObj), true, false, $crumbs);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new alias"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?></li>
			<?PHP
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/add_alias.php?Pub=<?php p($cPub); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
