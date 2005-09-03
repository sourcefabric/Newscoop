<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('AddArticle')) {
	CampsiteInterface::DisplayError(getGS("You do not have the right to add articles."));	
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
//todefnum('lpp', 10);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
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
CampsiteInterface::ContentTop(getGS('Add new article'), $topArray);

$sections = Section::GetSections($Pub, $Issue, $Language, array('LIMIT' => array('START' => $SectOffs, 'MAX_ROWS' => $ItemsToDisplay)));
$totalSections = Section::GetTotalSections($Pub, $Issue, $Language);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
	<TD><IMG SRC="/admin/img/tol.gif" BORDER="0"></TD>
	<TD><?php  putGS("Select the section"); ?></TD>
</TR>
</TABLE>
<P>
<?php 
if (count($sections) > 0) {	    
//    if ($NUM_ROWS) {
//	$nr= $NUM_ROWS;
//	$i= $lpp;
//    if($nr < $lpp)    $i = $nr;
	$color= 0;
	?>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Nr"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP">
			<B><?php  putGS("Name<BR><SMALL>(click to add article)</SMALL>");?></B>
		</TD>
	</TR>
	<?php 
	foreach ($sections as $section) {
    //for($loop=0;$loop<$i;$loop++) {
	//fetchRow($q_sect); ?>	
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD ALIGN="RIGHT">
			<?php p($section->getSectionId()); ?>
		</TD>
		<TD >
			<A HREF="/<?php p($ADMIN); ?>/articles/add.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($section->getIssueId()); ?>&Section=<?php p($section->getSectionId()); ?>&Language=<?php  p($section->getLanguageId()); ?>&Wiz=1"><?php p(htmlspecialchars($section->getName())); ?></A>
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
<?php CampsiteInterface::CopyrightNotice(); ?>
