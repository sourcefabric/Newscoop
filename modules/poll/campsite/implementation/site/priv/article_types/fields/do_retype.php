<?php
camp_load_translation_strings("article_type_fields");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to add article type fields."));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$fieldName = trim(Input::Get('f_field_name'));
$fieldType = trim(Input::Get('f_article_field_type'));
$rootTopicId = Input::Get('f_root_topic_id', 'int', 0);

$field =& new ArticleTypeField($articleTypeName, $fieldName);

$correct = true;
$errorMsgs = array();

if (!ArticleType::IsValidFieldName($fieldName)) {
	$errorMsgs[] = getGS('The $1  must not be void and may only contain letters and underscore (_) character.','<B>'.getGS('Name').'</B>');
	$correct = false;
}
if (!$field->exists()) {
	$errorMsgs[] = getGS('The field $1 does not already exist.', '<B>'.urlencode($fieldName).'</B>');
	$correct = false;
}

$validTypes = array('text', 'date', 'body', 'topic');
if (!in_array($fieldType, $validTypes)) {
	$errorMsgs[] = getGS('Invalid field type.');
	$correct = false;
}

if ($correct) {
	//$field->create($fieldType, $rootTopicId);
	$field->setType($fieldType, $rootTopicId);
	camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Article type fields"), "/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
$crumbs[] = array(getGS("Adding new field"), "");

echo camp_html_breadcrumbs($crumbs);

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new field"); ?> </B>
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
	<INPUT TYPE="button" class="button" NAME="Ok" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/fields/add.php?f_article_type=<?php print urlencode($articleTypeName); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
