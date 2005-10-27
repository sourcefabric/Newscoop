<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

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
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');
$IssOffs = Input::Get('IssOffs', 'int', 0, true);

$cName = trim(Input::Get('cName'));
$cShortName = trim(Input::Get('cShortName'));
$cLang = Input::Get('cLang');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));	
	exit;
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);

$correct = true;
$created = false;

if ($cLang == 0) {
    $correct = false;
}

if ($cName == "") {
    $correct = false; 
}

if ($cShortName == "") {
    $correct = false; 
}

if ($correct) {
    $newIssue = $issueObj->copy(null, $issueObj->getIssueId(), $cLang);
    $newIssue->setName($cName);
    $newIssue->setUrlName($cShortName);
    $logtext = getGS('Issue $1 added', $cName); 
    Log::Message($logtext, $User->getUserName(), 11);
    header("Location: /$ADMIN/issues/?Pub=$Pub");
    exit;
    //$created = true;
}

$tmpArray = array("Pub" => $publicationObj, "Issue" => $issueObj);
camp_html_content_top("Adding new translation", $tmpArray);
?>

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new translation"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><BLOCKQUOTE>
    <?php 
    if ($cLang == 0) {	?>
    	<LI><?php  putGS('You must select a language.'); ?></LI>
        <?php
    }
    
    if ($cName == "") { ?>
    	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
        <?php
    }
    
    if ($cShortName == "") { ?>
    	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>'); ?></LI>
        <?php
    }
    
    if ($created) { ?>
    	<LI><?php  putGS('The issue $1 has been successfuly added.','<B>'.htmlspecialchars($cName).'</B>' ); ?></LI>
        <?php
    } else {
        if ($correct != 0) { ?>
        	<LI><?php  putGS('The issue could not be added.'); ?></LI>
        	<LI><?php  putGS('Please check if another issue with the same number/language does not already exist.'); ?></LI>
            <?php
        }
    } ?>
    </BLOCKQUOTE>
    </TD>
</TR>

<?php  if ($correct && $created) { ?>	
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="Another" VALUE="<?php  putGS('Add another'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php p($Language); ?>'">
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/?Pub=<?php p($Pub); ?>'">
	</DIV>
	</TD>
</TR>
<?php  } else { ?>	
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php p($Issue); ?>'">
	</DIV>
	</TD>
</TR>
<?php  } ?>

</TABLE>
</CENTER>
<P>

<?php camp_html_copyright_notice(); ?>
</BODY>
</HTML>
