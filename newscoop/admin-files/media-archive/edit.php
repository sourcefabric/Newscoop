<?php
camp_load_translation_strings("media_archive");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Image.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ImageSearch.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

$f_image_id = Input::Get('f_image_id', 'int', 0);

if (!Input::IsValid()) {
	camp_html_goto_page("/$ADMIN/media-archive/index.php");
}
$imageObj = new Image($f_image_id);
$articles = ArticleImage::GetArticlesThatUseImage($f_image_id);

$crumbs = array();
$crumbs[] = array(getGS("Content"), "");
$crumbs[] = array(getGS("Media Archive"), "/$ADMIN/media-archive/index.php");
if ($g_user->hasPermission('ChangeImage')) {
	$crumbs[] = array(getGS('Change image information'), "");
}
else {
	$crumbs[] = array(getGS('View image'), "");
}
$breadcrumbs = camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

echo $breadcrumbs;

?>
<p></p>
<table cellpadding="0" cellspacing="0" class="action_buttons">
<tr>
<?php if ($g_user->hasPermission('AddImage')) { ?>
    <td>
        <A HREF="/<?php echo $ADMIN; ?>/media-archive/add.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0" alt="<?php  putGS('Add new image'); ?>"></A>
    </TD>
    <TD style="padding-left: 3px;">
        <A HREF="/<?php echo $ADMIN; ?>/media-archive/add.php"><B><?php  putGS('Add new image'); ?></B></A>
    </TD>
<?php } ?>
<?php if ($g_user->hasPermission('DeleteImage') && !$imageObj->inUse()) { ?>
    <td style="padding-left: 15px;">
        <A HREF="/<?php echo $ADMIN; ?>/media-archive/do_del.php?f_image_id=<?php echo $f_image_id; ?>&<?php echo SecurityToken::URLParameter();?>" onclick="return confirm('<?php putGS("Are you sure you want to delete the image \\'$1\\'?", camp_javascriptspecialchars($imageObj->getDescription())); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php putGS('Delete image $1',htmlspecialchars($imageObj->getDescription())); ?>"></A>
    </TD>
    <TD style="padding-left: 3px;">
        <A HREF="/<?php echo $ADMIN; ?>/media-archive/do_del.php?f_image_id=<?php echo $f_image_id; ?>&<?php echo SecurityToken::URLParameter();?>" onclick="return confirm('<?php putGS("Are you sure you want to delete the image \\'$1\\'?", camp_javascriptspecialchars($imageObj->getDescription())); ?>');"><b><?php putGS('Delete'); ?></b></a>
    </TD>
<?php } ?>
</tr>
</table>

<?php camp_html_display_msgs(); ?>
<p></p>
<IMG SRC="<?php echo $imageObj->getImageUrl(); ?>" BORDER="0" ALT="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" style="padding-left:15px">
<P>
<?php if ($g_user->hasPermission('ChangeImage')) { ?>
<FORM NAME="image_edit" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/media-archive/do_edit.php" ENCTYPE="multipart/form-data">
<?php echo SecurityToken::FormParameter(); ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Change image information"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Description"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_description" VALUE="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" SIZE="32" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Photographer"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_photographer" VALUE="<?php echo htmlspecialchars($imageObj->getPhotographer());?>" SIZE="32" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Place"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_place" VALUE="<?php echo htmlspecialchars($imageObj->getPlace()); ?>" SIZE="32" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
	<TD align="left">
	<input type="text" name="f_image_date" value="<?php echo htmlspecialchars($imageObj->getDate()); ?>" size="11" maxlength="10" class="input_text date" />
	</TD>
</TR>
<?php
if ($imageObj->getLocation() == 'remote') {
?>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL"); ?>:</TD>
	<TD align="left">
		<?php echo htmlspecialchars($imageObj->getUrl()); ?>
	</TD>
</TR>
<?php
} else {
?>
<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Image"); ?>:</TD>
	<TD align="left">
		<?php echo basename($imageObj->getImageStorageLocation()); ?>
	</TD>
</TR>
<?php
}
?>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="HIDDEN" NAME="f_image_id" VALUE="<?php echo $imageObj->getImageId(); ?>">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.forms.image_edit.f_image_description.focus();
</script>
<?php
} // if ($g_user->hasPermission('ChangeImage'))

if (count($articles) > 0) {
	// image is in use //////////////////////////////////////////////////////////////////
	?>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" width="370px" class="table_list">
	<tr class="table_list_header">
		<td><?php putGS('Used in articles'); ?>:</td>
		<td><?php putGS('Language'); ?></td>
	</tr>
	<?php
	$color = 0;
	$previousArticleNumber = -1;
	foreach ($articles as $article) {
		$translations = $article->getTranslations();
		foreach ($translations as $translation) {
			echo '<tr ';
			if ($color) {
				$color=0;
				echo 'class="list_row_even"';
			} else {
				$color=1;
				echo 'class="list_row_odd"';
			}
			echo '>';
			if ($translation->getArticleNumber() == $previousArticleNumber) {
				echo '<td class="translation_indent">';
			}
			else {
				echo '<td>';
			}
			echo "<a href=\"".camp_html_article_url($translation, $translation->getLanguageId(), "edit.php").'">'.htmlspecialchars($translation->getTitle()).'</a></td>';
			echo "<td>".$translation->getLanguageName()."</td>";
			echo "</tr>";
			$previousArticleNumber = $translation->getArticleNumber();
		}
	}
	?>
	</table>
<?php
}

camp_html_copyright_notice();
?>
