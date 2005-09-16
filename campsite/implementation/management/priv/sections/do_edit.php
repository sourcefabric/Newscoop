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

if ($correct) {
    $sectionObj->setName($cName);
    $sectionObj->setShortName($cShortName);
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
    $logtext = getGS('Section $1 updated to issue $2. $3 ($4) of $5', $cName, $Issue, 
            $issueObj->getName(), $issueObj->getLanguageName(), $publicationObj->getName()); 
    Log::Message($logtext, $User->getUserName(), 21);
    
}
else { 
    $errors[] = getGS('The section could not be changed.').' '.getGS('Please check if another section with the same number does not already exist.'); 
}


camp_html_content_top("Updating section name");
?>

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
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
	if ($correct) {
		if ($cSubs == "a") {
			if ($add_subs_res > 0) { ?>
				<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.encHTML(decS($numSubscriptionsAdded)).'</B>'); ?></LI>
            	<?php
			}
		}
		if ($cSubs == "d") {
			if ($del_subs_res > 0) { ?>
				<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.encHTML(decS($numSubscriptionsDeleted)).'</B>'); ?></LI>
                <?php
			}
		}
    }

    if ($modified) { ?>
    	<LI><?php  putGS('The section $1 has been successfuly modified.', '<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
        <?php  
    } 
    ?>		
    </BLOCKQUOTE>
    </TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <?php 
    if ($correct && $modified) { ?>
        <INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
        <?php  
    } else { ?>
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/sections/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>'">
        <?php  
    } ?>
    </DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
<P>

<?php camp_html_copyright_notice(); ?>
