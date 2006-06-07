<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to add issues.'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_name = trim(Input::Get('f_issue_name', 'string', ''));
$f_issue_number = trim(Input::Get('f_issue_number', 'int'));
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_url_name = Input::Get('f_url_name');

$correct = true;
$created = false;
$errorMsgs = array();
if ($f_language_id == 0) {
	$correct = false;
	$errorMsgs[] = getGS('You must select a language.');
}
if (empty($f_issue_name)) {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>');
}
if ($f_url_name == "") {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>');
}
if (!camp_is_valid_url_name($f_url_name)) {
	$correct = false;
	$errorMsgs[] = getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('URL Name') . '</B>');
}
if (empty($f_issue_number) || !is_numeric($f_issue_number) || ($f_issue_number <= 0)) {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Number').'</B>');
}

if (!Input::IsValid()) {
    $correct = false;
	$errorMsgs[] = getGS('Invalid Input: $1', Input::GetErrorString());
}

$publicationObj =& new Publication($f_publication_id);

if ($correct) {
    $newIssueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
    $created = $newIssueObj->create($f_url_name, array('Name' => $f_issue_name));
    if ($created) {
    	header("Location: /$ADMIN/issues/edit.php?Pub=$f_publication_id&Issue=$f_issue_number&Language=$f_language_id");
    	exit;
    }
}

camp_html_content_top(getGS('Adding new issue'), array('Pub' => $publicationObj), true, false, array(getGS("Issues") => "/$ADMIN/issues/?Pub=$f_publication_id"));
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
    		    <LI><?php  echo getGS('The issue could not be added.').' '.getGS('Please check if another issue with the same number/language does not already exist.'); ?></LI>
    		    <?php
            }
    	}
        ?>
        </BLOCKQUOTE>
    </TD>
</TR>

<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/add_new.php?Pub=<?php p($f_publication_id); ?>'">
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
