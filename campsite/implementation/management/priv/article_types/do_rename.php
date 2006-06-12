<?php
camp_load_translation_strings("article_types");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to rename article types."));
	exit;
}

$f_oldName = trim(Input::get('f_oldName'));
$f_name = trim(Input::Get('f_name'));

if ($f_oldName == $f_name) {
   	header("Location: /$ADMIN/article_types/");
	exit;
}

$correct = true;
$created = false;

$errorMsgs = array();
if (empty($f_name)) {
    $correct = false;
    $errorMsgs[] = getGS('You must complete the $1 field.','</B>'.getGS('Name').'</B>');
} else {
	$valid = ArticleType::IsValidFieldName($f_name);
	if (!$valid) {
		$correct = false;
		$errorMsgs[] = getGS('The $1 field may only contain letters and underscore (_) character.', '</B>' . getGS('Name') . '</B>');
    }

    if ($correct) {
    	$old_articleType =& new ArticleType($f_oldName);
    	if (!$old_articleType->exists()) {
		    $correct = false;
		    $errorMsgs[] = getGS('The article type $1 does not exist.', '<B>'.htmlspecialchars($f_oldName).'</B>');
		}
    }

	if ($correct) {
		$articleType =& new ArticleType($f_name);
		if ($articleType->exists()) {
			$correct = false;
			$errorMsgs[] = getGS('The article type $1 already exists.', '<B>'. htmlspecialchars($f_name). '</B>');
		}
	}

    if ($correct) {
    	$old_articleType->rename($f_name);
    	header("Location: /$ADMIN/article_types/");
		exit;
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Rename article type"), "");

echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Rename article type"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php
		foreach ($errorMsgs as $errorMsg) {
			echo "<li>".$errorMsg."</li>";
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/rename.php?f_name=<?php p($f_oldName); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
