<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to add issues.'));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$cNumber = trim(Input::Get('cNumber', 'int'));

$correct = true;
$created = false;
$errorMsgs = array();
if ($cNumber == "") {
	$correct = false; 
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>'); 
}

if (!Input::IsValid()) {
    $correct = false;
	$errorMsgs[] = getGS('Invalid Input: $1', Input::GetErrorString());
}

$publicationObj =& new Publication($Pub);

if ($correct) {
    $lastIssue =& Issue::GetLastCreatedIssue($Pub);
    $lastIssue->copy(null, $cNumber);
	$logtext = getGS('New issue $1 from $2 in publication $3', $cNumber, $lastIssue->getIssueId(), $publicationObj->getName()); 
	Log::Message($logtext, $User->getUserName(), 11);
	header("Location: /$ADMIN/issues/?Pub=$Pub");
	exit;
}

camp_html_content_top(getGS('Copying previous issue'), array('Pub' => $publicationObj), true, false, array(getGS("Issues") => "/$ADMIN/issues/?Pub=$Pub"));

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
	<INPUT TYPE="button" class="button" NAME="Ok" VALUE="<?php  putGS('Ok'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/?Pub=<?php  p($Pub); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>