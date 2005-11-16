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
$Language = Input::Get('Language', 'int', 0);
$cName = trim(Input::Get('cName', 'string', '', true));
$cNumber = trim(Input::Get('cNumber', 'int'));
$cSubs = Input::Get('cSubs', 'string', '', true);
$cShortName = trim(Input::Get('cShortName', 'string'));

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;    
}

$issueObj =& new Issue($Pub, $Language, $Issue);
$publicationObj =& new Publication($Pub);

if (!$publicationObj->exists()) {
    camp_html_display_error(getGS('Publication does not exist.'));
    exit; 
}
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('No such issue.'));
	exit;
}

$correct = true;
$created = false;
$isValidShortName = camp_is_valid_url_name($cShortName);

$errors = array();
if ($cName == "") {
	$correct = false; 
	$errors[] = getGS('You must complete the $1 field.', '"'.getGS('Name').'"'); 
}
if ($cNumber == "") {
	$correct= false;
	$cNumber = ($cNumber + 0); 
	$errors[] = getGS('You must complete the $1 field.','"'.getGS('Number').'"'); 
}
if ($cShortName == "") {
	$correct = false;
	$errors[] = getGS('You must complete the $1 field.','"'.getGS('URL Name').'"');
}
if (!$isValidShortName && trim($cShortName) != "") {
	$correct = false;
	$errors[] = getGS('The $1 field may only contain letters, digits and underscore (_) character.', '"' . getGS('URL Name') . '"');
}
if ($correct) {
    $newSection =& new Section($Pub, $Issue, $Language, $cNumber);
    $created = $newSection->create($cName, $cShortName);
    if ($created) {
	    if (!empty($cSubs)) {
	        $numSubscriptionsAdded = Subscription::AddSectionToAllSubscriptions($Pub, $cNumber);
			if ($numSubscriptionsAdded == -1) { 
	            $errors[] = getGS('Error updating subscriptions.'); 
			}
	    }
	    $logtext = getGS('Section $1 added to issue $2. $3 ($4) of $5',
	        $cName, $Issue, $issueObj->getName(), $issueObj->getLanguageName(), $publicationObj->getName()); 
	    Log::Message($logtext, $User->getUserName(), 21);
	    header("Location: edit.php?Pub=$Pub&Issue=$Issue&Language=$Language&Section=".$newSection->getSectionNumber());
	    exit;
    }
}

$tmpArray = array("Pub" => $publicationObj, "Issue" => $issueObj);
camp_html_content_top(getGS("Adding new section"), $tmpArray);
?> 

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new section"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><BLOCKQUOTE>
    <?php 
    foreach ($errors as $error) { ?>
		<LI><?php echo $error; ?></LI>
		<?php
	}
	if ($created) {    ?>
        <LI><?php  putGS('The section $1 has been successfuly added.','<B>'.htmlspecialchars($cName).'</B>'); ?></LI>
        <?php 
        if ($cSubs != "") {
			if ($numSubscriptionsAdded > 0) { ?>
				<LI><?php  putGS('A total of $1 subscriptions were updated.','<B>'.$numSubscriptionsAdded.'</B>'); ?></LI>
	           <?php
			}
		}
    } else {
        if ($correct != 0) { ?>
        	<LI><?php  putGS('The section could not be added.'); ?></LI>
        	<LI><?php  putGS('Please check if another section with the same number or URL name does not exist already.'); ?></LI>
            <?php  
        }
    }
    ?>
    </BLOCKQUOTE>
    </TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <?php  if ($correct && $created) { ?>
        <INPUT TYPE="button" class="button" NAME="Add another" VALUE="<?php  putGS('Add another'); ?>" ONCLICK="location.href='/admin/sections/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
<?php  } else { ?>
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/sections/add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
<?php  } ?>
	</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
<P>

<?php camp_html_copyright_notice(); ?>
