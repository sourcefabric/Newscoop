<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$IssOffs = Input::Get('IssOffs', 'int', 0, true);
if ($IssOffs < 0) {
	$IssOffs = 0;
}
$ItemsPerPage = 20;

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}
$publicationObj =& new Publication($Pub);
$allIssues = Issue::GetIssues($Pub, null, null, $publicationObj->getLanguageId(), array('LIMIT' => array('START' => $IssOffs, 'MAX_ROWS'=> $ItemsPerPage)));
$totalIssues = Issue::GetNumIssues($Pub);

CampsiteInterface::ContentTop(getGS('Issues'), array('Pub' => $publicationObj));

if ($User->hasPermission('ManageIssue')) {
	if (Issue::GetNumIssues($Pub) <= 0) {
		?>
		<P>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="add_new.php?Pub=<?php pencURL($Pub); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></A></TD>
			<TD><A HREF="add_new.php?Pub=<?php  pencURL($Pub); ?>" ><B><?php  putGS("Add new issue"); ?></B></A></TD>
		</TR>
		</TABLE>
	<?php  } else { ?>	
		<P>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="qadd.php?Pub=<?php  pencURL($Pub); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></A></TD>
			<TD><A HREF="qadd.php?Pub=<?php  pencURL($Pub); ?>" ><B><?php  putGS("Add new issue"); ?></B></A></TD>
		</TR>
		</TABLE>
	<?php  }
}
?>
<P>
<?php 
if (count($allIssues) > 0) {
	$color = 0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
	<?php  if ($User->hasPermission('ManageIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Nr"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to see sections)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("URL Name"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Language"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Published<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></B></TD>
	<?php if ($User->hasPermission('Publish')) { ?>
		<TD ALIGN="center" VALIGN="TOP" width="1%"><B><?php  putGS("Scheduled Publishing"); ?></B></TD>
	<?php } ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Translate"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Configure"); ?></B></TD> 
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Preview"); ?></B></TD>
	<?php  } else { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Nr"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to see sections)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Language"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Published<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Preview"); ?></B></TD>
	<?php  }
	
	if ($User->hasPermission('DeleteIssue')) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
	</TR>

<?php 
$currentIssue = -1;
foreach ($allIssues as $issue) {
	?>	
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
	
	<?php  if ($User->hasPermission('ManageIssue')) { ?>
	<TD ALIGN="RIGHT">
		<?php  
		if ($currentIssue != $issue->getIssueId()) {
			 p($issue->getIssueId());
		}
		else {
			print '&nbsp;';		
		} 
	 	?>
 	</TD>
 	
	<TD <?php if ($currentIssue == $issue->getIssueId()) { ?> style="padding-left: 20px;" <?php } ?>>
		<A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><?php p(htmlspecialchars($issue->getName())); ?></A>
	</TD>
	
	<TD>
		<?php p(htmlspecialchars($issue->getShortName())); ?>
	</TD>
	
	<TD >
		<?php p(htmlspecialchars($issue->getLanguageName())); ?>
	</TD>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/status.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><?php  if ($issue->getPublished() == 'Y') { p(htmlspecialchars($issue->getPublicationDate())); } else { print putGS("Publish"); } ?></A>
	</TD>
<?php if ($User->hasPermission('Publish')) { ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/automatic_publishing.png" alt="<?php putGS("Scheduled Publishing"); ?>"  title="<?php putGS("Scheduled Publishing"); ?>" border="0"></A>
	</TD>
<?php } ?>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/translate.png" alt="<?php  putGS("Translate"); ?>" title="<?php  putGS("Translate"); ?>" border="0"></A>
	</TD>
	<TD ALIGN="CENTER">
		<A HREF="/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/configure.png" alt="<?php  putGS("Configure"); ?>" title="<?php  putGS("Configure"); ?>"  border="0"></A>
	</TD>
<?php  } else { ?>
	<TD ALIGN="RIGHT">
		<?php p($issue->getIssueId()); ?>
	</TD>
	
	<TD >
		<A HREF="/<?php echo $ADMIN; ?>/issues/sections/?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>"><?php p(htmlspecialchars($issue->getName())); ?></A>
	</TD>
	
	<TD>
		<?php p($issue->getLanguageName()); ?>		
	</TD>
	
	<TD ALIGN="CENTER">
		<?php 
		if ($issue->getPublished() == 'Y') {
			p(htmlspecialchars($issue->getPublicationDate())); 
		}
		else {
			print putGS("No"); 
		}
		?>
	</TD>
	<?php  } ?>

	<TD ALIGN="CENTER">
		<A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/issues/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false"><img src="/<?php echo $ADMIN; ?>/img/icon/preview.png" alt="<?php  putGS("Preview"); ?>" title="<?php  putGS("Preview"); ?>" border="0"></A>
	</TD>

	<?php
    if ($User->hasPermission('DeleteIssue')) { ?> 
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/issues/do_del.php?Pub=<?php p($Pub); ?>&Issue=<?php  p($issue->getIssueId()); ?>&Language=<?php p($issue->getLanguageId()); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the issue $1?', htmlspecialchars($issue->getName())); ?>');"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete issue $1', htmlspecialchars($issue->getName())); ?>" title="<?php  putGS('Delete issue $1', htmlspecialchars($issue->getName())); ?>"></A>
		</TD>
	<?php  } ?>
	</TR>
	
	<?php 
    $currentIssue = $issue->getIssueId();
}
?>	
<TR>
	<TD COLSPAN="2" NOWRAP>
		<?php  
		if ($IssOffs > 0) { ?>
			<B><A HREF="index.php?Pub=<?php p($Pub); ?>&IssOffs=<?php print (max(0, $IssOffs - $ItemsPerPage)); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
			<?php  
		}
    	if ( ($IssOffs + $ItemsPerPage) < $totalIssues) {
    		if ($IssOffs > 0) {
    			echo " | ";
    		}
    		?>
    	    <B><A HREF="index.php?Pub=<?php p($Pub); ?>&IssOffs=<?php print (min(($totalIssues - 1), ($IssOffs + $ItemsPerPage))); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
			<?php  
    	} 
    	?>	
	</TD>
</TR>
</TABLE>
<?php  
} 
else { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No issues.'); ?></LI>
	</BLOCKQUOTE>
	<?php  
} ?>

<?php CampsiteInterface::CopyrightNotice(); ?>