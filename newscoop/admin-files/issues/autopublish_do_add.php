<?php
require_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/issues/issue_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/IssuePublish.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

// Check permissions
if (!$g_user->hasPermission('Publish')) {
    camp_html_display_error($translator->trans("You do not have the right to schedule issues or articles for automatic publishing."));
}

// Get input
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$event_id = Input::Get('event_id', 'int', null, true);
$publish_date = trim(Input::Get('publish_date', 'string', ''));
$action = trim(Input::Get('action', 'string', ''));
$publish_articles = trim(Input::Get('publish_articles', 'string', ''));
$publish_hour = trim(Input::Get('publish_hour', 'string', ''));
$publish_min = trim(Input::Get('publish_min', 'string', ''));

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString()) , 'issues'));
	exit;
}


$correct = ($publish_date != "") && ($publish_hour != "")
	&& ($publish_min != "") && ($action == "P" || $action == "U");

// Check that publish date is not in the past
$past_publish = 0;
if( date ("Y-m-d", time() ) == $publish_date ) {
	if(strlen($publish_hour) == 1) {
		$server_hour = date("G", time());
	} else {
		$server_hour = date("H", time());
	}
	if( $server_hour >= $publish_hour) {
		if( $server_hour > $publish_hour) {
			$correct = 0;
			$past_publish = 1;
		} else {
			if( date("i", time()) > $publish_min) {
				$correct = 0;
				$past_publish = 1;
			}
		}
	}
}

$publish_time = $publish_date . " " . $publish_hour . ":" . $publish_min . ":00";

// check publish/unpublish same time
foreach( IssuePublish::GetIssueEvents($Pub, $Issue, $Language, false ) as $evt ) {
    if( strtotime( $evt->m_data['time_action'] ) == strtotime( $publish_time )
        && ( $action != $evt->m_data['publish_action'] ) ) {
        $correct = false;
        $conflicting_action = true;
    }
}

if ($publish_articles != "Y" && $publish_articles != "N") {
	$publish_articles = "N";
}

$created = 0;
if ($correct) {
    $issuePublishExists = true;

    $issuePublishObj = new IssuePublish($event_id);
	if (!$issuePublishObj->exists()) {
	    $issuePublishObj->create();
	    $issuePublishObj->setPublicationId($Pub);
	    $issuePublishObj->setIssueNumber($Issue);
	    $issuePublishObj->setLanguageId($Language);
	    $issuePublishExists = false;

	}
    $issuePublishObj->setPublishAction($action);
    $issuePublishObj->setPublishArticlesAction($publish_articles);
    $issuePublishObj->setActionTime($publish_time);
	$created = 1;
}
if ($created) {

        $action = ($issuePublishExists) ? 'updated' : 'added';
        $issueObj = new Issue($Pub, $Language, $Issue);
        camp_html_goto_page("/$ADMIN/issues/edit.php?Pub=$Pub&Issue=$Issue&Language=$Language");
}

$issueObj = new Issue($Pub, $Language, $Issue);
$publicationObj = new Publication($Pub);
$crumbs = array("Pub" => $publicationObj, "Issue" => $issueObj);
camp_html_content_top($translator->trans("Scheduling a new publish action"), $crumbs);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
	<TR>
		<TD COLSPAN="2">
			<B> <?php echo $translator->trans("Scheduling a new publish action"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php
	if ( $publish_date == "" ) {
	$correct= 0; ?>	<LI><?php echo $translator->trans('You must fill in the $1 field.', array('$1' => '<B>'.$translator->trans('Date').'</B>')); ?></LI>
	<?php }

    if ( ($publish_hour == "") || ($publish_min == "") ) {
	$correct= 0; ?>	<LI><?php echo $translator->trans('You must fill in the $1 field.','<B>'. array('$1' => $translator->trans('Time').'</B>' )); ?></LI>
    <?php }

	if ( ($action != "P") && ($action != "U") ) {
	$correct= 0; ?>	<LI><?php echo $translator->trans('You must select an action.'); ?></LI>
    <?php }

	if ($past_publish) {
	?>	<LI><?php echo $translator->trans('The publishing schedule can not be set in the past', array(), 'issues'); ?></LI>
    <?php }

    if ($conflicting_action) {
	?>	<LI><?php echo $translator->trans('The publishing/unpublishing can not be set the same time', array(), 'issues'); ?></LI>
    <?php }

	if ($correct) {
		if (!$created) { ?>
			<LI><?php echo $translator->trans('There was an error scheduling the $1 action on $2', array('$1' => $translator->trans($action_str), '$2' => $publish_time), 'issues'); ?></LI>
	       <?php
		}
    }
?>	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
