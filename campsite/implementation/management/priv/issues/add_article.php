<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('AddArticle')) {
	camp_html_display_error(getGS('You do not have the right to add articles.'));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$IssOffs = Input::Get('IssOffs', 'int', 0, true);
if ($IssOffs < 0) {
	$IssOffs = 0;
}
$ItemsPerPage = 10;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));	
	exit;
}
$publicationObj =& new Publication($Pub);

$issues = Issue::GetIssues($Pub, null, null, null, array('ORDER BY' => array('Number' => 'DESC'), 'LIMIT' => array('START' => $IssOffs, 'MAX_ROWS' => ($ItemsPerPage+1))));

camp_html_content_top(getGS('Add new article'), array('Pub' => $publicationObj));

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
    <TD><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/tol.gif" BORDER="0"></TD>
    <TD><?php  putGS("Select the issue"); ?></TD>
</TR>
</TABLE>

<P>
<?php 
if (count($issues) > 0) {
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Nr"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Name"); ?></B></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Published<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></B></TD>
</TR>

<?php 
$color= 0;
$previousIssueId = -1; 
foreach ($issues as $issue) {
	?>	
	<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD ALIGN="RIGHT" >
            <?php p($issue->getIssueId()); ?>
        </TD>
		
        <TD <?php if ($previousIssueId == $issue->getIssueId()) { ?> style="padding-left: 20px;" <?php } ?>>
			<A HREF="/<?php echo $ADMIN; ?>/sections/add_article.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><?php p(htmlspecialchars($issue->getName())); ?></A> (<?php p(htmlspecialchars($issue->getLanguageName())); ?>)
		</TD>
		
        <TD ALIGN="CENTER">
			<?php 
			if ($issue->getPublished() == 'Y') {
			    p($issue->getPublicationDate());
			}
			else {
			    p('No');
			}
			?>
		</TD>
	</TR>
    <?php 
    $previousIssueId = $issue->getIssueId();
}
?>	
<TR>
    <TD COLSPAN="2" NOWRAP>
        <?php if ($IssOffs > 0) { ?>
        	<B><A HREF="add_article.php?Pub=<?php p($Pub); ?>&IssOffs=<?php  p($IssOffs - $ItemsPerPage); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
        <?php  }

        if ($nr >= $ItemsPerPage+1) { ?>
        	 | <B><A HREF="add_article.php?Pub=<?php  p($Pub); ?>&IssOffs=<?php p($IssOffs + $ItemsPerPage); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
        <?php  } ?>
    </TD>
</TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No issues.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>