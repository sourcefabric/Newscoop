<?php
camp_load_translation_strings("article_types");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to rename article type fields."));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$f_oldName = Input::Get('f_old_field_name');
$f_name = Input::Get('f_new_field_name');

if ($f_oldName == $f_name) {
   	camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=". urlencode($articleTypeName));
}
$correct = true;
$created = false;

$errorMsgs = array();
if (empty($f_name)) {
    $correct = false;
    $errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>');
} else {
	$valid = ArticleType::IsValidFieldName($f_name);
	if (!$valid) {
		$correct = false;
		$errorMsgs[] = getGS('The $1 field may only contain letters and underscore (_) character.', '<B>' . getGS('Name') . '</B>');
    }

    if ($correct) {
    	$old_articleTypeField =& new ArticleTypeField($articleTypeName, $f_oldName);
    	if (!$old_articleTypeField->exists()) {
		    $correct = false;
		    $errorMsgs[] = getGS('The field $1 does not exist.', '<B>'.htmlspecialchars($f_oldName).'</B>');
		}
    }

	if ($correct) {
		$articleTypeField =& new ArticleTypeField($articleTypeName, $f_name);
		if ($articleTypeField->exists()) {
			$correct = false;
			$errorMsgs[] = getGS('The field $1 already exists.', '<B>'. htmlspecialchars($f_name). '</B>');
		}
	}

    if ($correct) {
    	$old_articleTypeField->rename($f_name);
    	camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=". urlencode($articleTypeName));
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Renaming article type field"), "");

echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Renaming article type field"); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/fields/rename.php?f_article_type=<?php print $articleTypeName; ?>&f_field_name=<?php p($f_oldName); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
