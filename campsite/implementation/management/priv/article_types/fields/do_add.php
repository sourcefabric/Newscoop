<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_type_fields");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to add article type fields."));
	exit;
}

$articleTypeName = Input::Get('AType');
$fieldName = trim(Input::Get('cName'));
$fieldType = trim(Input::Get('cType'));

$field =& new ArticleTypeField($articleTypeName, $fieldName);

$created = false; 
$correct = true;
$errorMsgs = array();

if (!ArticleType::IsValidFieldName($fieldName)) {
	$errorMsgs[] = getGS('The $1  must not be void and may only contain letters and underscore (_) character.','<B>'.getGS('Name').'</B>');
	$correct = false;
}
if ($field->exists()) {
	$errorMsgs[] = getGS('The field $1 already exists.', '<B>'.urlencode($fieldName).'</B>');
	$correct = false;
}

$validTypes = array('text', 'date', 'body');
if (!in_array($fieldType, $validTypes)) {
	$errorMsgs[] = getGS('Invalid field type.');
	$correct = false;
}

if ($correct) {
	$field->create($fieldType);
	$created = true;
	header("Location: /$ADMIN/article_types/fields/?AType=".urlencode($articleTypeName));
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Article type fields"), "/$ADMIN/article_types/fields/?AType=".urlencode($articleTypeName));
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
	<INPUT TYPE="button" class="button" NAME="Ok" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/fields/add.php?AType=<?php print urlencode($articleTypeName); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
