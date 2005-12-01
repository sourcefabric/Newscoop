<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('AddArticle')) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}
$PubOffs = Input::Get('PubOffs', 'int', 0, true);
if ($PubOffs < 0) {
    $PubOffs = 0;
}
$ItemsPerPage = 20;

$sqlOptions = array("LIMIT" => array("START" => $PubOffs, "MAX_ROWS" => ($ItemsPerPage+1)), 
                    "ORDER BY" => array("Name" => "ASC"));
$publications = Publication::GetPublications($sqlOptions);
$numPublications = Publication::GetNumPublications();

$crumbs = array();
$crumbs[] = array("Content", "");
$crumbs[] = array("Add new article", "");
$crumbs[] = array("Publications", "");
echo camp_html_breadcrumbs($crumbs);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="indent">
<TR>
	<TD>
		<IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0">
	</TD>
	<TD >
		<b><?php  putGS("Select the publication"); ?></b>
	</TD>
</TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="6" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Name<BR><SMALL>(click to select the publication)</SMALL>"); ?></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Site"); ?></TD>
	</TR>
<?php 
	$color=0;
	foreach ($publications as $publication) {?>
		<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD>
			<A HREF="/<?php p($ADMIN); ?>/issues/add_article.php?Pub=<?php p($publication->getPublicationId()); ?>"><?php p(htmlspecialchars($publication->getName())); ?></A>
		</TD>
		<TD >
			<?php p(htmlspecialchars($publication->getProperty("Alias"))); ?>&nbsp;
		</TD>
    </TR>
<?php 
}
?>
<TR><TD COLSPAN="2" NOWRAP>
<?php 
if ($PubOffs > 0) { ?>
	<B><A HREF="add_article.php?PubOffs=<?php echo (max(0, ($PubOffs - $ItemsPerPage))); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  
}
if (($PubOffs + $ItemsPerPage) < $numPublications) { ?>
	 | <B><A HREF="add_article.php?PubOffs=<?php  print ($PubOffs + $ItemsPerPage); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php camp_html_copyright_notice(); ?>
