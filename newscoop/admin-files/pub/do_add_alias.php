<?php

require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/pub/pub_common.php");
require_once($GLOBALS['g_campsiteDir']."/classes/Alias.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('ManagePub')) {
	camp_html_display_error($translator->trans("You do not have the right to manage publications.", array(), 'pub'));
	exit;
}

$cPub = Input::Get('cPub', 'int');
$cName = trim(Input::Get('cName'));

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($cPub);
$backLink = "/$ADMIN/pub/add_alias.php?Pub=$cPub";

$correct = true;
$created = false;
$errorMsgs = array();
if (empty($cName)) {
	$correct = false;
	$errorMsgs[] = $translator->trans('You must fill in the $1 field.', array('$1' => '<B>Name</B>'));
}

$aliases = 0;
if ($correct) {
	$aliasDups = count(Alias::GetAliases(null, null, $cName));
	if ($aliasDups <= 0) {
		$newAlias = new Alias();
		$created = $newAlias->create(array('Name' => "$cName", "IdPublication" => "$cPub"));
		if ($created) {
			$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
			$cacheService->clearNamespace('publication');
			camp_html_goto_page("/$ADMIN/pub/aliases.php?Pub=$cPub");
		}
	}
	else {
		$errorMsgs[] = $translator->trans('Another alias with the same name exists already.', array(), 'pub');
	}
}

if (!$created && !$correct) {
	$errorMsgs[] = $translator->trans('The site alias $1 could not be added.', array('$1' => '<B>'.$cName.'</B>'), 'pub');
}

$crumbs = array($translator->trans("Publication Aliases", array(), 'pub') => "aliases.php?Pub=$cPub");
camp_html_content_top($translator->trans("Adding new alias", array(), 'pub'), array("Pub" => $publicationObj), true, false, $crumbs);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Adding new alias", array(), 'pub'); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/add_alias.php?Pub=<?php p($cPub); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
