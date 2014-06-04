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

$Pub = Input::Get('Pub', 'int', 0);
$Alias = Input::Get('Alias', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($Pub);
$aliasObj = new Alias($Alias);
$errorMsgs = array();

if ($publicationObj->getDefaultAliasId() != $Alias) {
        $aliasName = $aliasObj->getName();
	$deleted = $aliasObj->delete();
	$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
	$cacheService->clearNamespace('publication');

	if ($deleted) {
		camp_html_goto_page("/$ADMIN/pub/aliases.php?Pub=$Pub");
	} else {
		$errorMsgs[] = $translator->trans('The alias $1 could not be deleted.', array('$1' => '<B>'.$aliasObj->getName().'</B>'), 'pub');
	}
} else {
	$errorMsgs[] = $translator->trans('$1 is the default publication alias, it can not be deleted.', array('$1' => '<B>'.$aliasObj->getName().'</B>'), 'pub');
}

$crumbs = array($translator->trans("Publication Aliases", array(), 'pub') => "aliases.php?Pub=$Pub");
camp_html_content_top($translator->trans("Deleting alias", array(), 'pub'), array("Pub" => $publicationObj), true, false, $crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Deleting alias", array(), 'pub'); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/aliases.php?Pub=<?php p($Pub); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
