<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_type_fields");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$articleType =& new ArticleType($articleTypeName);
$fields = $articleType->getUserDefinedColumns();

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Article type fields"), "");

echo camp_html_breadcrumbs($crumbs);

if ($User->hasPermission("ManageArticleTypes")) { ?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
<TR>
	<TD><A HREF="add.php?f_article_type=<?php print urlencode($articleTypeName); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
	</TD>
	<TD><A HREF="add.php?f_article_type=<?php print urlencode($articleTypeName); ?>" ><B><?php  putGS("Add new field"); ?></B></A>
	</TD>
	<TD><A HREF="javascript: void(0);" ONCLICK="ShowAll(field_ids);"><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/add.png" BORDER="0"></A></TD>
	<TD><A HREF="javascript: void(0);" ONCLICK="ShowAll(field_ids);"><?php putGS("Show display names"); ?></A></TD>

</TR>
</TABLE>
<?php  } ?>

<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php putGS("Order"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Template Field Name"); ?></B></TD>

	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Type"); ?></B></TD>
	<?php  if ($User->hasPermission("ManageArticleTypes")) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Display Name"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Translate"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Show/Hide"); ?></B></TD>

	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
</TR>
<?php 
$color= 0;
$i = 0;
foreach ($fields as $field) { 
	if ($field->m_metadata[0]['is_hidden'] == 1) {
		$hideShowText = 'show';
		$hideShowImage = "is_hidden.png";
	}
	else {
		$hideShowText = 'hide';
		$hideShowImage = "is_shown.png";
	}
?>

<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
	<TD>
		<TABLE><TR>
		<TD>
		<DIV STYLE="width: 15px;">
			<?php if (count($fields) > 1 && $i < count($fields) - 1) { ?><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/down_arrow.png"><?php } else { ?>&nbsp;<?php } ?>
		</DIV>
		</TD>
		<TD>
		<DIV STYLE="width: 15px;">
			<?php if (count($fields) > 1 && $i != 0) { ?><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/up_arrow.png"><?php } else { ?>&nbsp;<?php } ?>
		</DIV>
		</TD>
		</TR></TABLE>
	</TD>
	
	<TD>
		<A HREF="rename.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php print $field->getName(); ?>"><?php  print htmlspecialchars($field->getPrintName()); ?></A>&nbsp;
	</TD>
	
	<TD>
		<A HREF="retype.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_field_name=<?php print $field->getName(); ?>"><?php print htmlspecialchars($field->getPrintType()); ?></A>		
	</TD>

	<TD>
		<?php print htmlspecialchars($field->getDisplayName()); ?>
	</TD>
	
	<td> 
		<a href="javascript: void(0);" onclick="HideAll(field_ids); ShowElement('translate_field_<?php p($i); ?>');"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/localizer.png" alt="<?php putGS("Translate"); ?>" title="<?php putGS("Translate"); ?>" border="0"></a>
	</td>

	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/article_types/fields/do_hide.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_article_type_field_name=<?php  print urlencode($field->getName()); ?>&f_status=<?php print $hideShowText; ?>" onclick="return confirm('<?php putGS('Are you sure you want to $1 the article type field $2?', $hideShowText, $field->getPrintName()); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php echo $hideShowImage; ?>" BORDER="0" ALT="<?php  putGS('$1 article type field $2', ucfirst($hideShowText), $field->getPrintName()); ?>" TITLE="<?php  putGS('$1 article type $2', ucfirst($hideShowText), $field->getPrintName()); ?>" ></A>
	</TD>

	<?php  if ($User->hasPermission("ManageArticleTypes")) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/article_types/fields/do_del.php?f_article_type=<?php print urlencode($articleTypeName); ?>&f_article_field=<?php print urlencode($field->getName()); ?>" onclick="return confirm('<?php echo getGS('Are you sure you want to delete the field $1?', htmlspecialchars($field->getPrintName())).' '.getGS('You will also delete all fields with this name from all articles of this type from all publications.');  ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete field $1', htmlspecialchars($field->getPrintName())); ?>" TITLE="<?php  putGS('Delete field $1', htmlspecialchars($field->getPrintName())); ?>" ></A>
	</TD>
	<?php  
	$i++;	
	} ?>
</TR>
<?php  } // foreach  ?>	
</TABLE>
<?php camp_html_copyright_notice(); ?>
