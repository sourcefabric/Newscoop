<?php
require_once($_SERVER['DOCUMENT_ROOT']."/classes/common.php");
load_common_include_files("$ADMIN_DIR/pub/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/IssuePublish.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('Publish')) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to schedule issues or articles for automatic publishing." )));
	exit;
}

// Get input
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$publish_date = trim(Input::Get('publish_date', 'string', ''));
$action = trim(Input::Get('action', 'string', ''));
$publish_articles = trim(Input::Get('publish_articles', 'string', ''));
$publish_hour = trim(Input::Get('publish_hour', 'string', ''));
$publish_min = trim(Input::Get('publish_min', 'string', ''));

$correct = $publish_date != "" && $publish_hour != ""
	&& $publish_min != "" && ($action == "P" || $action == "U");

if ($publish_articles != "Y" && $publish_articles != "N")
	$publish_articles = "N";

$created = 0;
if ($correct) {
	$action_str = $action == "P" ? "Publish" : "Unpublish";
	$publish_time = $publish_date . " " . $publish_hour . ":" . $publish_min . ":00";
	//$sql = "select * from IssuePublish where IdPublication = $Pub and NrIssue = $Issue and IdLanguage = $Language and PublishTime = '$publish_time'";
	//query($sql, 'q_issp');
    $action =& new IssuePublish($Pub, $Issue, $Language, $publish_time);
	if ($action->exists()) {
	    $action->setPublishAction($action);
	    $action->setPublishArticlesAction($publish_articles);
		//$sql = "update IssuePublish set Action = '$action', PublishArticles = '$publish_articles' where IdPublication = $Pub and NrIssue = $Issue and IdLanguage = $Language and PublishTime = '$publish_time'";
		//query($sql);
		$created = 1;
	} else {
	    $action->create();
		//$sql = "INSERT IGNORE INTO IssuePublish SET IdPublication = $Pub, NrIssue = $Issue, IdLanguage = $Language, PublishTime = '$publish_time', Action = '$action', PublishArticles = '$publish_articles'";
		//query ($sql);
		//$created = $AFFECTED_ROWS > 0;
		$created = 1;
	}
    $action->setPublishAction($action);
    $action->setPublishArticlesAction($publish_articles);
}
if ($created) {
	header("Location: /$ADMIN/pub/issues/autopublish.php?Pub=$Pub&Issue=$Issue&Language=$Language");
}

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD><?php  putGS("Scheduling a new publish action"); ?></TD>
		<TD ALIGN=RIGHT>
			<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
			<TR>
				<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/?Pub=<?php p($Pub); ?>" class="breadcrumb"><?php  putGS("Issues");  ?></A></TD>
				<td class="breadcrumb_separator">&nbsp;</td>
				<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" class="breadcrumb"><?php  putGS("Publications");  ?></A></TD>
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
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ALIGN="CENTER" class="table_input">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Scheduling a new publish action"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php
	if ( $publish_date == "" ) {
	$correct= 0; ?>	<LI><?php putGS('You must complete the $1 field.','<B>'.getGS('Date').'</B>' ); ?></LI>
	<?php }

    if ( ($publish_hour == "") || ($publish_min == "") ) {
	$correct= 0; ?>	<LI><?php putGS('You must complete the $1 field.','<B>'.getGS('Time').'</B>' ); ?></LI>
    <?php }

	if ( ($action != "P") && ($action != "U") ) {
	$correct= 0; ?>	<LI><?php putGS('You must select an action.'); ?></LI>
    <?php }

	if ($correct) {
		if ($created) { ?>
			<LI><?php putGS('The $1 action has been scheduled on $2', getGS($action_str), $publish_time); ?></LI>
            <?php 
		} else { ?>
			<LI><?php putGS('There was an error scheduling the $1 action on $2', getGS($action_str), $publish_time); ?></LI>
	       <?php 
		}
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
</TABLE>
<P>

<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php } else { ?><BLOCKQUOTE>
	<LI><?php putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php } ?>
<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>

</HTML>
