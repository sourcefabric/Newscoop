<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");
require_once($GLOBALS['g_campsiteDir']. '/classes/SimplePager.php');
camp_load_translation_strings("api");

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$SectOffs = camp_session_get("SectOffs_".$Pub."_".$Issue."_".$Language, 0);
if ($SectOffs < 0)	{
	$SectOffs = 0;
}
$ItemsPerPage = 15;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}
$publicationObj = new Publication($Pub);
$issueObj = new Issue($Pub, $Language, $Issue);
$allSections = Section::GetSections($Pub, $Issue, $Language, null, null, array('ORDER BY' => 'Number', 'LIMIT' => array('START' => $SectOffs, 'MAX_ROWS' => $ItemsPerPage)), true);
$totalSections = Section::GetTotalSections($Pub, $Issue, $Language);

$pager = new SimplePager($totalSections, $ItemsPerPage, "SectOffs_".$Pub."_".$Issue."_".$Language, "index.php?Pub=$Pub&Issue=$Issue&Language=$Language&");

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj);
camp_html_content_top(getGS('Section List'), $topArray);
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/issues/?Pub=<?php  p($Pub); ?>"><B><?php  putGS("Issue List"); ?></B></A></TD>
<?php
if ($g_user->hasPermission('ManageSection')) { ?>
    <TD style="padding-left: 20px;"><A HREF="/<?php echo $ADMIN; ?>/sections/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
    <TD><A HREF="/<?php echo $ADMIN; ?>/sections/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><B><?php  putGS("Add new section"); ?></B></A></TD>
<?php  } ?>
</TR>
</TABLE>
<P>
<?php
if (count($allSections) > 0) {
	$color=0;
?>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"><?php putGS("Number"); ?></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php putGS("Name<BR><SMALL>(click to see articles)</SMALL>"); ?></TD>
    <TD ALIGN="LEFT" VALIGN="TOP"><?php putGS("No. of Articles<BR><SMALL>(Published/Total)</SMALL>"); ?></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php putGS("URL Name"); ?></TD>
        <?php if ($g_user->hasPermission('ManageSection')) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php putGS("Configure"); ?></TD>
        <?php } ?>
	<?php if ($g_user->hasPermission('ManageSection') && $g_user->hasPermission('AddArticle')) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Duplicate"); ?></TD>
	<?php } ?>
	<?php if($g_user->hasPermission('DeleteSection')) { ?>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Delete"); ?></TD>
	<?php } ?>
</TR>
<?php
    foreach ($allSections as $section) {
        $numberOfArticles = Article::GetArticles($section->getPublicationId(),
                                                 $section->getIssueNumber(),
                                                 $section->getSectionNumber(),
                                                 $section->getLanguageId(),
                                                 null, true);
        $whereOptions = array("Published='Y'");
        $numberOfPublishedArticles = Article::GetArticles($section->getPublicationId(),
                                                          $section->getIssueNumber(),
                                                          $section->getSectionNumber(),
                                                          $section->getLanguageId(),
                                                          null, true,
                                                          $whereOptions);
?>
	<TR <?php if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD ALIGN="RIGHT">
			<?php p($section->getSectionNumber()); ?>
		</TD>
		<TD >
              <A HREF="/<?php p($ADMIN); ?>/articles/?f_publication_id=<?php p($Pub); ?>&f_issue_number=<?php  p($section->getIssueNumber()); ?>&f_section_number=<?php p($section->getSectionNumber()); ?>&f_language_id=<?php  p($section->getLanguageId()); ?>"><?php p(htmlspecialchars($section->getName())); ?></A>
		</TD>
        <TD ALIGN="CENTER">
            <?php echo $numberOfPublishedArticles.' / '.$numberOfArticles; ?>
        </TD>
		<TD>
			<?php p(htmlspecialchars($section->getUrlName())); ?>
		</TD>
        <?php if ($g_user->hasPermission('ManageSection')) { ?>
        <TD ALIGN="CENTER">
			<A HREF="/<?php p($ADMIN); ?>/sections/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($section->getIssueNumber()); ?>&Section=<?php p($section->getSectionNumber()); ?>&Language=<?php  p($section->getLanguageId()); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/configure.png" alt="<?php  putGS("Configure"); ?>" title="<?php  putGS("Configure"); ?>" border="0"></A>
        </TD>
        <?php } ?>
		<?php if ($g_user->hasPermission('ManageSection') && $g_user->hasPermission('AddArticle')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php p($ADMIN);?>/sections/duplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php p($section->getSectionNumber()); ?>&Language=<?php  p($Language); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/duplicate.png" alt="<?php putGS('Duplicate'); ?>" title="<?php putGS('Duplicate'); ?>" border="0"></A>
		</TD>
		<?php } ?>

		<?php if ($g_user->hasPermission('DeleteSection')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php p($ADMIN); ?>/sections/del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($section->getIssueNumber()); ?>&Section=<?php p($section->getSectionNumber()); ?>&Language=<?php  p($section->getLanguageId()); ?>&SectOffs=<?php p($SectOffs); ?>&<?php echo SecurityToken::URLParameter(); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php putGS('Delete section $1', htmlspecialchars($section->getName())); ?>" TITLE="<?php  putGS('Delete section $1', htmlspecialchars($section->getName())); ?>"></A>
		</TD>
		<?php  } ?>
	</TR>
<?php
} // foreach
?>
</table>
<table class="indent">
<TR>
	<TD>
		<?php echo $pager->render(); ?>
	</TD>
</TR>
</TABLE>
<?php
} // if
else { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No sections'); ?></LI>
	</BLOCKQUOTE>
	<?php
}

camp_html_copyright_notice(); ?>
