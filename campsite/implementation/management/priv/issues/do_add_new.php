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
$cName = trim(Input::Get('cName', 'string', ''));
$cNumber = trim(Input::Get('cNumber', 'int'));
$cLang = Input::Get('cLang', 'int', 0);
$cShortName = Input::Get('cShortName');

$correct = true;
$created = false;
$errorMsgs = array();
if ($cLang == 0) {
	$correct = false;
	$errorMsgs[] = getGS('You must select a language.');
}
if ($cName == "") {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>');
}
if ($cShortName == "") {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>');
}
if (!camp_is_valid_url_name($cShortName)) {
	$correct = false;
	$errorMsgs[] = getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('URL Name') . '</B>');
}
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
    $newIssueObj =& new Issue($Pub, $cLang, $cNumber);
    $created = $newIssueObj->create($cShortName, array('Name' => $cName));
    if ($created) {
    	header("Location: /$ADMIN/issues/edit.php?Pub=$Pub&Issue=$cNumber&Language=$cLang");
    	exit;
    }
}

camp_html_content_top(getGS('Adding new issue'), array('Pub' => $publicationObj), true, false, array(getGS("Issues") => "/$ADMIN/issues/?Pub=$Pub"));
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new issue"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
    	<BLOCKQUOTE>
        <?php 
        if (!$correct) {
            foreach ($errorMsgs as $errorMsg) {
                ?>
                <li><?php echo $errorMsg; ?></li>
                <?php
            }
        }
        else {
            if (!$created) { ?>
    		    <LI><?php  putGS('The issue could not be added.'); ?></LI>
    		    <LI><?php  putGS('Please check if another issue with the same number/language does not already exist.'); ?></LI>
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
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/add_new.php?Pub=<?php p($Pub); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
