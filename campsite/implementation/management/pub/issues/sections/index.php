<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/section_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$SectOffs = Input::Get('SectOffs', 'int', 0, true);
if ($SectOffs < 0)	{
	$SectOffs= 0;
}
$ItemsPerPage = 15;

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;		
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$allSections =& Section::GetSections($Pub, $Issue, $Language, array('ORDER BY' => 'Number', 'LIMIT' => array('START' => $SectOffs, 'MAX_ROWS' => $ItemsPerPage)));
$totalSections = Section::GetTotalSections($Pub, $Issue, $Language);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj);
CampsiteInterface::ContentTop('Sections', $topArray);


if ($User->hasPermission('ManageSection')) { ?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
	<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="/admin/img/icon/add.png" BORDER="0"></A></TD>
	<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><B><?php  putGS("Add new section"); ?></B></A></TD>
</TR>
</TABLE>
<?php  } ?>

<P>
<?php 
if (count($allSections) > 0) {
	$color=0;
?>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%"><?php  putGS("Nr"); ?></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php putGS("Name<BR><SMALL>(click to see articles)</SMALL>"); ?></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php putGS("URL Name"); ?></TD>
	<?php if ($User->hasPermission('ManageSection')) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%"><?php  putGS("Configure"); ?></TD>
	<?php } ?>
	<?php if ($User->hasPermission('ManageSection') && $User->hasPermission('AddArticle')) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%"><?php  putGS("Duplicate"); ?></TD>
	<?php } ?>
	<?php if($User->hasPermission('DeleteSection')) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%"><?php  putGS("Delete"); ?></TD>
	<?php } ?>
</TR>
<?php 
	foreach ($allSections as $section) { ?>	
	<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		
		<TD ALIGN="RIGHT">
			<?php  p($section->getSectionId()); ?>
		</TD>
		
		<TD >
			<A HREF="/admin/pub/issues/sections/articles/?Pub=<?php p($Pub); ?>&Issue=<?php  p($section->getIssueId()); ?>&Section=<?php p($section->getSectionId()); ?>&Language=<?php  p($section->getLanguageId()); ?>"><?php p(htmlspecialchars($section->getName())); ?></A>
		</TD>
		
		<TD >
			<?php p(htmlspecialchars($section->getShortName())); ?>
		</TD>
		
		<?php  if ($User->hasPermission('ManageSection')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/admin/pub/issues/sections/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($section->getIssueId()); ?>&Section=<?php p($section->getSectionId()); ?>&Language=<?php  p($section->getLanguageId()); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/configure.png" alt="<?php  putGS("Configure"); ?>" title="<?php  putGS("Configure"); ?>" border="0"></A>
		</TD>
		<?php 	} ?>
		
		<?php if ($User->hasPermission('ManageSection') && $User->hasPermission('AddArticle')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/admin/pub/issues/sections/duplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php p($section->getSectionId()); ?>&Language=<?php  p($Language); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/duplicate.png" alt="<?php putGS('Duplicate'); ?>" title="<?php putGS('Duplicate'); ?>" border="0"></A>
		</TD>
		<?php } ?>
		
		<?php if ($User->hasPermission('DeleteSection')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/admin/pub/issues/sections/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($section->getIssueId()); ?>&Section=<?php p($section->getSectionId()); ?>&Language=<?php  p($section->getLanguageId()); ?>&SectOffs=<?php p($SectOffs); ?>"><IMG SRC="/admin/img/icon/delete.png" BORDER="0" ALT="<?php putGS('Delete section $1', htmlspecialchars($section->getName())); ?>" TITLE="<?php  putGS('Delete section $1', htmlspecialchars($section->getName())); ?>"></A>
		</TD>
		<?php  } ?>
	</TR>
<?php 
} // foreach
?>	
<TR>
	<TD COLSPAN="2" NOWRAP>
		<?php  if ($SectOffs > 0) { ?>
		<B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&SectOffs=<?php  p($SectOffs - $ItemsPerPage); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
	<?php  }
	    if (($SectOffs + $ItemsPerPage) < $totalSections) {
	    	if ($SectOffs > 0) { echo " | "; }
	    	?>
			<B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&SectOffs=<?php  p ($SectOffs + $ItemsPerPage); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
		<?php  } ?>	
	</TD></TR>
</TABLE>
<?php 
} // if
else { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No sections'); ?></LI>
	</BLOCKQUOTE>
	<?php  
}

CampsiteInterface::CopyrightNotice(); ?>