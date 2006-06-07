<?php
camp_load_translation_strings("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");

$f_image_id = Input::Get('f_image_id', 'int', 0);

if (!Input::IsValid()) {
	header("Location: /$ADMIN/imagearchive/index.php");
	exit;
}
$imageObj =& new Image($f_image_id);
$articles = ArticleImage::GetArticlesThatUseImage($f_image_id);

$crumbs = array();
$crumbs[] = array(getGS("Content"), "");
$crumbs[] = array(getGS("Image Archive"), "/$ADMIN/imagearchive/index.php");
if ($g_user->hasPermission('ChangeImage')) {
	$crumbs[] = array(getGS('Change image information'), "");
}
else {
	$crumbs[] = array(getGS('View image'), "");
}
$breadcrumbs = camp_html_breadcrumbs($crumbs);

echo $breadcrumbs;
?>
<p></p>
<table cellpadding="0" cellspacing="0" class="action_buttons">
<tr>
<?php
if ($g_user->hasPermission('AddImage')) { ?>
    <td>
    	<A HREF="add.php"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0" alt="<?php  putGS('Add new image'); ?>"></A>
    </TD>
    <TD style="padding-left: 3px;">
    	<A HREF="add.php"><B><?php  putGS('Add new image'); ?></B></A>
    </TD>
<?php } ?>

</tr>
</table>
<p></p>
<IMG SRC="<?php echo $imageObj->getImageUrl(); ?>" BORDER="0" ALT="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" style="padding-left:15px">
<P>
<?php if ($g_user->hasPermission('ChangeImage')) { ?>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php" ENCTYPE="multipart/form-data">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Change image information"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Description"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_description" VALUE="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" SIZE="32" MAXLENGTH="128" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Photographer"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_photographer" VALUE="<?php echo htmlspecialchars($imageObj->getPhotographer());?>" SIZE="32" MAXLENGTH="64" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Place"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_place" VALUE="<?php echo htmlspecialchars($imageObj->getPlace()); ?>" SIZE="32" MAXLENGTH="64" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Date"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_date" VALUE="<?php echo htmlspecialchars($imageObj->getDate()); ?>" SIZE="11" MAXLENGTH="10" class="input_text"> <?php putGS('YYYY-MM-DD'); ?>
	</TD>
</TR>
<?php
if ($imageObj->getLocation() == 'remote') {
?>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("URL"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_uRL" VALUE="<?php echo htmlspecialchars($imageObj->getUrl()); ?>" SIZE="32" class="input_text">
	</TD>
</TR>
<?php
} else {
?>
<TR>
	<TD ALIGN="RIGHT"><?php  putGS("Image"); ?>:</TD>
	<TD align="left">
	<INPUT TYPE="TEXT" NAME="f_image_file" SIZE="32" MAXLENGTH="64" VALUE="<?php echo basename($imageObj->getImageStorageLocation()); ?>" DISABLED class="input_text">
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
<?php
} // if ($g_user->hasPermission('ChangeImage'))

if (count($articles) > 0) {
	// image is in use //////////////////////////////////////////////////////////////////
	?>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" width="370px" class="table_list" style="margin-left: 4px;">
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