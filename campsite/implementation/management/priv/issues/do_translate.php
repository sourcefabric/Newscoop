<?php
camp_load_translation_strings("issues");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to add issues.'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_id = Input::Get('f_issue_number', 'int');
$f_language_id = Input::Get('f_language_id', 'int');

$f_name = trim(Input::Get('f_name'));
$f_url_name = trim(Input::Get('f_url_name'));
$f_new_language_id = Input::Get('f_new_language_id');

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}
$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_id);

$correct = true;
$created = false;

if ($f_new_language_id == 0) {
    $correct = false;
}

if ($f_name == "") {
    $correct = false;
}

if ($f_url_name == "") {
    $correct = false;
}

if ($correct) {
    $newIssue = $issueObj->copy(null, $issueObj->getIssueNumber(), $f_new_language_id);
    $newIssue->setName($f_name);
    $newIssue->setUrlName($f_url_name);
    header("Location: /$ADMIN/issues/?Pub=$f_publication_id");
    exit;
    //$created = true;
}

$tmpArray = array("Pub" => $publicationObj, "Issue" => $issueObj);
camp_html_content_top(getGS("Adding new translation"), $tmpArray);
?>

<P>
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
    if ($f_new_language_id == 0) {	?>
    	<LI><?php  putGS('You must select a language.'); ?></LI>
        <?php
    }

    if ($f_name == "") { ?>
    	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
        <?php
    }

    if ($f_url_name == "") { ?>
    	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>'); ?></LI>
        <?php
    }

    if ($created) { ?>
    	<LI><?php  putGS('The issue $1 has been successfuly added.','<B>'.htmlspecialchars($f_name).'</B>' ); ?></LI>
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
	<INPUT TYPE="button" class="button" NAME="Another" VALUE="<?php  putGS('Add another'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/translate.php?Pub=<?php  p($f_publication_id); ?>&Issue=<?php  p($f_issue_id); ?>&Language=<?php p($f_language_id); ?>'">
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/?Pub=<?php p($f_publication_id); ?>'">
	</DIV>
	</TD>
</TR>
<?php  } else { ?>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/issues/translate.php?Pub=<?php  p($f_publication_id); ?>&Issue=<?php p($f_issue_id); ?>'">
	</TD>
</TR>
<?php  } ?>

</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
