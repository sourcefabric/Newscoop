<?php
camp_load_translation_strings("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

$f_image_id = Input::Get('f_image_id', 'int', 0);

if (!Input::IsValid() || ($f_image_id <= 0)) {
	header("Location: /$ADMIN/imagearchive/index.php");
	exit;
}

$imageObj =& new Image($f_image_id);

// This file can only be accessed if the user has the right to delete images.
if (!$g_user->hasPermission('DeleteImage')) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if ($imageObj->inUse()) {
	header("Location: /$ADMIN/imagearchive/index.php");
	exit;
}

$errorMsgs = $imageObj->delete();
if (!is_array($errorMsgs)) {
	// Go back to article image list.
	header("Location: /$ADMIN/imagearchive/index.php");
	exit;
}

$crumbs = array();
$crumbs[] = array(getGS('Content'), "");
$crumbs[] = array(getGS("Image Archive"), "/$ADMIN/imagearchive/index.php");
$crumbs[] = array(getGS("Delete image"), "");
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;
?>
<br>
<br>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Delete image"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
	    <?php
	   	foreach ($errorMsgs as $errorMsg) { ?>
	   		<li><?php echo $errorMsg; ?></li>
	   		<?php
	   	}
	   	?>
		</BLOCKQUOTE>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/imagearchive/index.php'">
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
