<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('DeleteIssue')) {
	camp_html_display_error(getGS('You do not have the right to delete issues.'));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');
$IssOffs = Input::Get('IssOffs', 'int', 0, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));	
	exit;
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);

$doDelete = true;
$articlesRemaining = Article::GetArticles($Pub, $Issue, null, $Language);
$numArticlesRemaining = count($articlesRemaining);
if ($numArticlesRemaining > 0) {
    $doDelete = false;
}

if ($doDelete) {
    $issueObj->delete(true, false);
	header("Location: /$ADMIN/issues/index.php?Pub=$Pub&IssOffs=$IssOffs");
	exit;
}

$tmpArray = array("Pub" => $publicationObj, "Issue"=> $issueObj);
camp_html_content_top("Deleting issue", $tmpArray);
?>
    
<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Deleting issue"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><BLOCKQUOTE>
    <?php 
    if ($numArticlesRemaining > 0) { ?>	
        <LI><?php  putGS('You cannot delete the issue because it is not empty (number of articles: $1).  To delete the issue, first delete its contents.', $numArticlesRemaining); ?></LI>
        <?php
    }
   	?>
	</BLOCKQUOTE>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/?Pub=<?php echo $Pub; ?>&IssOffs=<?php echo $IssOffs; ?>'">
	</DIV>
	</TD>
</TR>
</TABLE></CENTER>
<P>
<?php camp_html_copyright_notice(); ?>
</BODY>

</HTML>