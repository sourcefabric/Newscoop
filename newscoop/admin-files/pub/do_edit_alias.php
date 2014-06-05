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

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_alias_id = Input::Get('f_alias_id', 'int');
$f_name = trim(Input::Get('f_name'));

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())), $_SERVER['REQUEST_URI']);
	exit;
}

$publicationObj = new Publication($f_publication_id);

$correct = true;
$errorMsgs = array();

if (empty($f_name)) {
	$correct = false;
	$errorMsgs[] = $translator->trans('You must fill in the $1 field.', array('$1' => '<B>Name</B>'));
}

$alias = new Alias($f_alias_id);
$aliases = 0;
if ($correct) {
	if ($alias->getName() != $f_name) {
		$aliasDups = count(Alias::GetAliases(null, null, $f_name));
		if ($aliasDups <= 0) {
			$success = $alias->setName($f_name);
		}
		else {
			$errorMsgs[] = $translator->trans('Another alias with the same name exists already.', array(), 'pub');
			$correct = false;
		}
	}
}

if ($correct) {
	$cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
	$cacheService->clearNamespace('publication');
	
	camp_html_goto_page("/$ADMIN/pub/aliases.php?Pub=$f_publication_id&Alias=$f_alias_id");
	exit;
} else {
	$errorMsgs[] = $translator->trans('The site alias $1 could not be modified.', array('$1' => '<B>'.$alias->getName().'</B>'));
}

$crumbs = array($translator->trans("Publication Aliases", array(), 'pub') => "aliases.php?Pub=$f_publication_id");
camp_html_content_top($translator->trans("Editing alias", array(), 'pub'), array("Pub" => $publicationObj), true, false, $crumbs);

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Editing alias", array(), 'pub'); ?> </B>
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
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/aliases.php?Pub=<?php p($f_publication_id); ?>&Alias=<?php p($f_alias_id); ?>'">
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
