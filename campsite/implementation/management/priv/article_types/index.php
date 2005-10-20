<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_types");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$articleTypes = ArticleType::GetArticleTypes();

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "");

echo camp_html_breadcrumbs($crumbs);

if ($User->hasPermission("ManageArticleTypes")) { ?>
	<P>
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons">
	<TR>
		<TD><A HREF="add.php?Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
		<TD><A HREF="add.php?Back=<?php  print urlencode($_SERVER['REQUEST_URI']); ?>" ><B><?php  putGS("Add new article type"); ?></B></A></TD>
	</TR>
	</TABLE>
<?php  } ?>
<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Type"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Fields"); ?></B></TD>
	<?php  if ($User->hasPermission("DeleteArticleTypes")) { ?>		
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
</TR>
<?php 
$color = 0;
foreach ($articleTypes as $articleType) {
    ?>	
    <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
	<TD>
		<?php  print htmlspecialchars($articleType); ?>&nbsp;
	</TD>
	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/article_types/fields/?AType=<?php  print urlencode($articleType); ?>"><?php  putGS('Fields'); ?></A>
	</TD>
	
	<?php  if ($User->hasPermission("DeleteArticleTypes")) { ?>		
	<TD ALIGN="CENTER">
		<A HREF="/<?php p($ADMIN); ?>/article_types/do_del.php?AType=<?php  print urlencode($articleType); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the article type $1?', htmlspecialchars($articleType)); ?>');"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete article type $1', htmlspecialchars($articleType)); ?>" TITLE="<?php  putGS('Delete article type $1', htmlspecialchars($articleType)); ?>" ></A>
	</TD>
	<?php  } ?>	
	
	</TR>
	<?php  } // foreach  ?>	    
</TABLE>
<?php camp_html_copyright_notice(); ?>
