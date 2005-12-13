<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/sections/section_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageSection')) {
	camp_html_display_error(getGS("You do not have the right to add sections."));	
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$cSubs = Input::Get('cSubs', 'string', '', true);
$cShortName = trim(Input::Get('cShortName', 'string'));
$cSectionTplId = Input::Get('cSectionTplId', 'int', 0);
$cArticleTplId = Input::Get('cArticleTplId', 'int', 0);
$cName = Input::Get('cName');

if ($cSectionTplId < 0) {
    $cSectionTplId = 0;
}

if ($cArticleTplId < 0) {
    $cArticleTplId = 0;
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;    
}

$issueObj =& new Issue($Pub, $Language, $Issue);
$publicationObj =& new Publication($Pub);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);

if (!$publicationObj->exists()) {
    camp_html_display_error(getGS('Publication does not exist.'));
    exit; 
}
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('No such issue.'));
	exit;
}

$correct = true;
$modified = false;

$errors = array();
if ($cName == "") { 
    $correct = false; 
    $errors[] = getGS('You must complete the $1 field.','"'.getGS('Name').'"'); 
}
if ($cShortName == "")  {
	$correct = false;
	$errors[] = getGS('You must complete the $1 field.','"'.getGS('URL Name').'"');
}
$isValidShortName = camp_is_valid_url_name($cShortName);

if (!$isValidShortName) {
	$correct = false;
	$errors[] = getGS('The $1 field may only contain letters, digits and underscore (_) character.', '"' . getGS('URL Name') . '"');
}

$editUrl = "/$ADMIN/sections/edit.php?Pub=$Pub&Issue=$Issue&Language=$Language&Section=$Section";
if ($correct) {
    $sectionObj->setName($cName);
    $sectionObj->setUrlName($cShortName);
    $sectionObj->setSectionTemplateId($cSectionTplId);
    $sectionObj->setArticleTemplateId($cArticleTplId);
	$modified = true;

	if ($cSubs == "a") {
        $numSubscriptionsAdded = Subscription::AddSectionToAllSubscriptions($Pub, $Section);
		if ($numSubscriptionsAdded < 0) { 
			$errors[] = getGS('Error updating subscriptions.'); 
		}
	}
	if ($cSubs == "d") {
		$numSubscriptionsDeleted = Subscription::DeleteSubscriptionsInSection($Pub, $Section);
		if ($numSubscriptionsDeleted < 0) { 
            $errors[] = getGS('Error updating subscriptions.'); 
		}
	}
    $logtext = getGS('Section #$1 "$2" updated. (Publication: $3, Issue: $4)', 
    				 $Issue, $cName, $publicationObj->getPublicationId(), $issueObj->getIssueNumber()); 
    Log::Message($logtext, $User->getUserName(), 21);
    header("Location: $editUrl");
    exit;
}
else { 
    $errors[] = getGS('The section could not be changed.').' '.getGS('Please check if another section with the same number or URL name does not exist already.'); 
}

$topArray = array("Pub" => $publicationObj, "Issue" => $issueObj, "Section" => $sectionObj);
camp_html_content_top("Updating section name", $topArray);
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Updating section name"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<BLOCKQUOTE>
    <?php 
    foreach ($errors as $error) {
        echo "<LI>".$error."</LI>";
    }
    ?>
    </BLOCKQUOTE>
    </TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='<?php p($editUrl); ?>'">
    </DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
