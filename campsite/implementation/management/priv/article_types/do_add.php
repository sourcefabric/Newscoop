<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_types");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to add article types."));
	exit;
}

$cName = trim(Input::Get('cName')); 
$correct = true;
$created = false;

$errorMsgs = array();
if (empty($cName)) {
    $correct = false;
    $errorMsgs[] = getGS('You must complete the $1 field.','</B>'.getGS('Name').'</B>');
} else {
	$valid = ArticleType::IsValidFieldName($cName);
	if (!$valid) {
		$correct = false; 
		$errorMsgs[] = getGS('The $1 field may only contain letters and underscore (_) character.', '</B>' . getGS('Name') . '</B>'); 
    }

    if ($correct) {
    	$articleType =& new ArticleType($cName);
    	if ($articleType->exists()) {
		    $correct = false; 
		    $errorMsgs[] = getGS('The article type $1 already exists.', '<B>'.htmlspecialchars($cName).'</B>'); 
		}
    }
    
    if ($correct) {
    	$created = $articleType->create();
    	header("Location: /$ADMIN/article_types/fields/add.php?AType=$cName");
    	exit;
	}
} 

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Adding new article type"), "");

echo camp_html_breadcrumbs($crumbs);

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new article type"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/add.php'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
