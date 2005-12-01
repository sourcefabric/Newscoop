<?php 
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleType.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddArticle')) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);

$allArticleTypes = ArticleType::GetArticleTypes();
$allLanguages = Language::GetLanguages();

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile ();
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj);
camp_html_content_top(getGS('Add new article'), $topArray, true, true, array(getGS("Articles") => "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"));

?>

<?php
if (sizeof($allArticleTypes) == 0) {
?>
<p><table border="0" cellspacing="0" cellpadding="6" align="center" class="table_input">
	<tr>
		<td align="center">
		<font color="red">
		<?php putGS("No article types were defined. You must create an article type first."); ?>
		</font>
		<p><b><a href="/<?php echo $ADMIN; ?>/a_types/"><?php putGS("Edit article types"); ?></a></b></p>
		</td>
	</tr>
</table></p>
<?php
} else {
?>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($f_issue_number); ?>">
<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php p($f_section_number); ?>">
<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new article"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_article_name" SIZE="64" MAXLENGTH="140" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
	<TD>
		<SELECT NAME="f_article_type" class="input_select" alt="select" emsg="<?php putGS('You must complete the $1 field.', getGS('Article Type')); ?>">
		<option></option>
		<?php 
		foreach ($allArticleTypes as $tmpType) {
			echo '<OPTION>'.htmlspecialchars($tmpType).'</option>';
		}
		?>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
	<TD>
		<SELECT NAME="f_article_language" class="input_select">
		<?php 
	 	foreach ($allLanguages as $tmpLanguage) {
			echo '<option value="'.$tmpLanguage->getLanguageId().'"';
			if ($tmpLanguage->getLanguageId() == $f_language_id) {
				echo "selected";
			}
			echo '>'.$tmpLanguage->getName().'</option>';
        }
		?>			
		</SELECT>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
		</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php } ?>
<?php camp_html_copyright_notice(); ?>