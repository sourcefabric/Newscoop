<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("$ADMIN_DIR/pub/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");

todefnum('TOL_UserId');
todefnum('TOL_UserKey');
query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
$access=($NUM_ROWS != 0);
if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	if ($NUM_ROWS){
		fetchRow($XPerm);
	} else
		$access = 0;	//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	$xpermrows= $NUM_ROWS;
} else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
}

if ($access) {
	query ("SELECT Publish FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'Publish') == "Y");
	} else {
		$access = 0;
	}
}

todef('publish_date');
todefnum('publish_hour');
todefnum('publish_min');
todef('action');
todef('publish_articles');
if ($access) {
	global $created;

	$publish_date = trim($publish_date);
	$publish_hour = trim($publish_hour);
	$publish_min = trim($publish_min);

	$correct = trim($publish_date) != "" && trim($publish_hour) != ""
		&& trim($publish_min) != "" && ($action == "P" || $action == "U");

	if ($publish_articles != "Y" && $publish_articles != "N")
		$publish_articles = "N";

	$created = 0;
	if ($correct) {
		$action_str = $action == "P" ? "Publish" : "Unpublish";
		$publish_time = $publish_date . " " . $publish_hour . ":" . $publish_min . ":00";
		$sql = "select * from IssuePublish where IdPublication = $Pub and NrIssue = $Issue and IdLanguage = $Language and PublishTime = '$publish_time'";
		query($sql, 'q_issp');
		if ($NUM_ROWS > 0) {
			$sql = "update IssuePublish set Action = '$action', PublishArticles = '$publish_articles' where IdPublication = $Pub and NrIssue = $Issue and IdLanguage = $Language and PublishTime = '$publish_time'";
			query($sql);
			$created = 1;
		} else {
			$sql = "INSERT IGNORE INTO IssuePublish SET IdPublication = $Pub, NrIssue = $Issue, IdLanguage = $Language, PublishTime = '$publish_time', Action = '$action', PublishArticles = '$publish_articles'";
			query ($sql);
			$created = $AFFECTED_ROWS > 0;
		}
	}
	if ($created)
		header("Location: /$ADMIN/pub/issues/autopublish.php?Pub=$Pub&Issue=$Issue&Language=$Language");
}

?>

<HEAD>
	<TITLE><?php  putGS("Scheduling a new publish action"); ?></TITLE>
<?php if ($access == 0) { ?>
	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/<?php echo $ADMIN; ?>/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to schedule issues or articles for automatic publishing." )); ?>">
<?php } ?></HEAD>

<?php if ($access) { ?>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<?php
	todefnum('Pub');
	todefnum('Issue');
	todefnum('Language');
?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD><?php  putGS("Scheduling a new publish action"); ?></TD>
		<TD ALIGN=RIGHT>
			<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
			<TR>
				<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php p($Pub); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
				<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD><TD><A HREF="/<?php echo $ADMIN; ?>/pub/" ><B><?php  putGS("Publications");  ?></B></A></TD>
			</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>

<?php
	query ("SELECT * FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Publications WHERE Id=$Pub", 'q_pub');
	    if ($NUM_ROWS) {
		query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
		fetchRow($q_iss);
		fetchRow($q_pub);
		fetchRow($q_lang);
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php pgetHVar($q_pub,'Name'); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php pgetHVar($q_iss,'Number'); ?>. <?php pgetHVar($q_iss,'Name'); ?> (<?php pgetHVar($q_lang,'Name'); ?>)</TD>
</TR>
</TABLE>

<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" BGCOLOR="#C0D0FF" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Scheduling a new publish action"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php
	if ($publish_date == "" || $publish_date == " ") {
	$correct= 0; ?>	<LI><?php putGS('You must complete the $1 field.','<B>'.getGS('Date').'</B>' ); ?></LI>
	<?php }

    if ($publish_hour == "" || $publish_hour == " " || $publish_min == "" || $publish_min == " ") {
	$correct= 0; ?>	<LI><?php putGS('You must complete the $1 field.','<B>'.getGS('Time').'</B>' ); ?></LI>
    <?php }

	if ($action != "P" && $action != "U") {
	$correct= 0; ?>	<LI><?php putGS('You must select an action.'); ?></LI>
    <?php }

	if ($correct) {
		if ($created) { ?>			<LI><?php putGS('The $1 action has been scheduled on $2', getGS($action_str), $publish_time); ?></LI>
		<?php } else { ?>			<LI><?php putGS('There was an error scheduling the $1 action on $2', getGS($action_str), $publish_time); ?></LI>
	<?php }
}
?>	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/autopublish.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<P>

<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php }
CampsiteInterface::CopyrightNotice();
?>
</BODY>

</HTML>
