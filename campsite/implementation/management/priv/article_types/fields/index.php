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

$articleTypeName = Input::Get('AType');
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
	<TD><A HREF="add.php?AType=<?php print urlencode($articleTypeName); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
	</TD>
	<TD><A HREF="add.php?AType=<?php print urlencode($articleTypeName); ?>" ><B><?php  putGS("Add new field"); ?></B></A>
	</TD>
</TR>
</TABLE>
<?php  } ?>

<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Name"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Type"); ?></B></TD>
	<?php  if ($User->hasPermission("ManageArticleTypes")) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
</TR>
<?php 
$color= 0;
foreach ($fields as $field) { ?>
<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
	<TD>
		<?php  print htmlspecialchars($field->getPrintName()); ?>&nbsp;
	</TD>
	
	<TD>
		<?php print htmlspecialchars($field->getPrintType()); ?>		
	</TD>

	<?php  if ($User->hasPermission("ManageArticleTypes")) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/article_types/fields/do_del.php?AType=<?php print urlencode($articleTypeName); ?>&Field=<?php print urlencode($field->getPrintName()); ?>" onclick="return confirm('<?php echo getGS('Are you sure you want to delete the field $1?', htmlspecialchars($field->getPrintName())).' '.getGS('You will also delete all fields with this name from all articles of this type from all publications.');  ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete field $1', htmlspecialchars($field->getPrintName())); ?>" TITLE="<?php  putGS('Delete field $1', htmlspecialchars($field->getPrintName())); ?>" ></A>
	</TD>
	<?php  } ?>
</TR>
<?php  } // foreach  ?>	
</TABLE>
<?php camp_html_copyright_notice(); ?>
