<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to add issues.'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_number = trim(Input::Get('f_issue_number', 'int'));

$correct = true;
$created = false;
$errorMsgs = array();
if ( empty($f_issue_number) || !is_numeric($f_issue_number) || ($f_issue_number <= 0) ) {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>');
}

if (!Input::IsValid()) {
    $correct = false;
	$errorMsgs[] = getGS('Invalid Input: $1', Input::GetErrorString());
}

$publicationObj =& new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	$correct = false;
	$errorMsgs[] = getGS("Publication does not exist.");
}

// check if the issue number already exists
$existingIssues = Issue::GetIssues($f_publication_id, null, $f_issue_number);
if (count($existingIssues) > 0) {
	$correct = false;
	$errorMsgs[] = getGS("The issue could not be added.")." ".getGS("Please check if another issue with the same number/language does not already exist.");
}

if ($correct) {
    $lastIssue = Issue::GetLastCreatedIssue($f_publication_id);
    $issueCopies = $lastIssue->copy(null, $f_issue_number);
    $issueCopy = array_pop($issueCopies);
	$logtext = getGS('New issue $1 from $2 in publication $3', $f_issue_number,
					 $lastIssue->getIssueNumber(), $publicationObj->getName());
	Log::Message($logtext, $g_user->getUserName(), 11);
	header("Location: /$ADMIN/issues/edit.php?Pub=$f_publication_id&Issue=".$issueCopy->getIssueNumber()
		   ."&Language=".$issueCopy->getLanguageId());
	exit;
}

camp_html_content_top(getGS('Copying previous issue'), array('Pub' => $publicationObj), true, false, array(getGS("Issues") => "/$ADMIN/issues/?Pub=$f_publication_id"));

?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Copying previous issue"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
    	<BLOCKQUOTE>
    	<?php
    	foreach ($errorMsgs as $errorMsg) { ?>
    	   <LI><?php echo $errorMsg; ?></LI>
    	   <?PHP
    	}
    	?>
    	</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="Ok" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/add_prev.php?Pub=<?php  p($f_publication_id); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>