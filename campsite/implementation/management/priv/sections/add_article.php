<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('AddArticle')) {
	camp_html_display_error(getGS("You do not have the right to add articles."));	
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$SectOffs = Input::Get('SectOffs', 'int', 0, true);
if ($SectOffs < 0) {
	$SectOffs= 0;
}
$ItemsToDisplay = 15;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;		
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$newSectionNumber = Section::GetUnusedSectionId($Pub, $Issue, $Language);

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile ();
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj);
camp_html_content_top(getGS('Add new article'), $topArray);

$sections = Section::GetSections($Pub, $Issue, $Language, array('LIMIT' => array('START' => $SectOffs, 'MAX_ROWS' => $ItemsToDisplay)));
$totalSections = Section::GetTotalSections($Pub, $Issue, $Language);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="indent">
<TR>
	<TD><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/tol.gif" BORDER="0"></TD>
	<TD><b><?php  putGS("Select the section"); ?></b></TD>
</TR>
</TABLE>
<P>
<?php 
if (count($sections) > 0) {	    
	$color= 0;
	?>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3"class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Nr"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP">
			<B><?php  putGS("Name<BR><SMALL>(click to add article)</SMALL>");?></B>
		</TD>
	</TR>
	<?php 
	foreach ($sections as $section) { ?>	
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD ALIGN="RIGHT">
			<?php p($section->getSectionNumber()); ?>
		</TD>
		<TD >
			<A HREF="/<?php p($ADMIN); ?>/articles/add.php?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php  p($section->getIssueNumber()); ?>&f_section_number=<?php p($section->getSectionNumber()); ?>&f_language_id=<?php  p($section->getLanguageId()); ?>"><?php p(htmlspecialchars($section->getName())); ?></A>
		</TD>
	</TR>
	<?php 
	} // foreach
	?>	
	<TR>
		<TD COLSPAN="2" NOWRAP>
		<?php  if ($SectOffs > 0) { ?>
        	<B><A HREF="add_article.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&SectOffs=<?php p(max(0, ($SectOffs - $ItemsToDisplay))); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
		<?php  } ?>
		<?php  if ( ($SectOffs + $ItemsToDisplay) < $totalSections) { 
			if ($SectOffs > 0) { echo "|"; }
			?>
        	<B><A HREF="add_article.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&SectOffs=<?php  p ($SectOffs + $ItemsToDisplay); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
		<?php  } ?>	
		</TD>
	</TR>
</TABLE>
<?php } // if ?>
<?php camp_html_copyright_notice(); ?>
