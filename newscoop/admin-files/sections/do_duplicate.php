<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission("ManageSection")) {
	camp_html_display_error($translator->trans("You do not have the right to add sections.", array(), 'sections'));
	exit;
}
if (!$g_user->hasPermission("AddArticle")) {
	camp_html_display_error($translator->trans("You do not have the right to add articles."));
	exit;
}

$f_src_publication_id = Input::Get('f_src_publication_id', 'int', 0);
$f_src_issue_number = Input::Get('f_src_issue_number', 'int', 0);
$f_src_section_number = Input::Get('f_src_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_dest_publication_id = Input::Get('f_dest_publication_id', 'int', 0);
$f_dest_issue_number = Input::Get('f_dest_issue_number', 'int', 0);
$radioButton = Input::Get('f_section_chooser', 'string', 'new_section');
if ($radioButton == 'new_section') {
    $f_dest_section_number = Input::Get('f_dest_section_new_number', 'int', 0, true);
}
else {
    $f_dest_section_number = Input::Get('f_dest_section_existing', 'int', 0, true);
}
$f_dest_section_name = Input::Get('f_dest_section_new_name', 'string', '', true);

if (!Input::IsValid()) {
   	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString())));
	exit;
}

$srcPublicationObj = new Publication($f_src_publication_id);
if (!$srcPublicationObj->exists()) {
	camp_html_display_error($translator->trans('Publication does not exist.'));
	exit;
}

$srcIssueObj = new Issue($f_src_publication_id, $f_language_id, $f_src_issue_number);
if (!$srcIssueObj->exists()) {
	camp_html_display_error($translator->trans('Issue does not exist.'));
	exit;
}

$srcSectionObj = new Section($f_src_publication_id, $f_src_issue_number, $f_language_id, $f_src_section_number);
if (!$srcSectionObj->exists()) {
	camp_html_display_error($translator->trans('Section does not exist.'));
	exit;
}

$correct = ($f_dest_publication_id > 0) && ($f_dest_issue_number > 0) && ($f_dest_section_number > 0);
$created = false;

if ($correct) {
    $dstSectionObj = $srcSectionObj->copy($f_dest_publication_id, $f_dest_issue_number,
    									  $f_language_id, $f_dest_section_number);
    if (($radioButton == "new_section") && ($f_dest_section_name != "")) {
        $dstSectionObj->setName($f_dest_section_name);
    }
	$dstPublicationObj = new Publication($f_dest_publication_id);
	$dstIssueObj = new Issue($f_dest_publication_id, $f_language_id, $f_dest_issue_number);
	$created = true;

    $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
    $cacheService->clearNamespace('section');

	camp_html_goto_page("/$ADMIN/sections/duplicate_complete.php?"
		   ."f_src_publication_id=$f_src_publication_id"
		   ."&f_src_issue_number=$f_src_issue_number"
		   ."&f_src_section_number=$f_src_section_number"
		   ."&f_language_id=$f_language_id"
		   ."&f_dest_publication_id=$f_dest_publication_id"
		   ."&f_dest_issue_number=$f_dest_issue_number"
		   ."&f_dest_section_number=$f_dest_section_number");
} else {
	$topArray = array('Pub' => $srcPublicationObj, 'Issue' => $srcIssueObj, 'Section' => $srcSectionObj);
	camp_html_content_top($translator->trans('Duplicating section', array(), 'sections'), $topArray);
}

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  echo $translator->trans("Duplicating section", array(), 'sections'); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php
			if (!$correct) {
				echo "<LI>"; echo $translator->trans('Invalid parameters received'); echo "</LI>\n";
			} else {
				 ?>
				<LI><?php  echo $translator->trans('The section $1 could not be duplicated', array('$1' => '<B>'.htmlspecialchars($srcSectionObj->getName()).'</B>'), 'sections'); ?></LI>
		<?php  } ?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
           <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/sections/?Pub=<?php  p($f_src_publication_id); ?>&Issue=<?php  p($f_src_issue_number); ?>&Language=<?php  p($f_language_id); ?>'">
		</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>