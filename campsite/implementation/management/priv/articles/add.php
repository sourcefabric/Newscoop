<?php 
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddArticle')) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$Back = Input::Get('Back', 'string', 'index.php', true);
$Wiz = Input::Get('Wiz', 'int', 0, true);
if ($Wiz != 0) {
	$Back = "/$ADMIN/home.php";
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$languageObj =& new Language($Language);

$allArticleTypes = ArticleType::GetArticleTypes();
$allLanguages = Language::GetLanguages();

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile ();
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj);
camp_html_content_top(getGS('Add new article'), $topArray, true, true, array(getGS("Articles") => "/$ADMIN/articles/?Pub=$Pub&Issue=$Issue&Section=$Section&Language=$Language"));

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
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new article"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="140" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
	<TD>
		<SELECT NAME="cType" class="input_select" alt="select" emsg="<?php putGS('You must complete the $1 field.', getGS('Article Type')); ?>">
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
		<SELECT NAME="cLanguage" class="input_select">
		<?php 
	 	foreach ($allLanguages as $tmpLanguage) {
			echo '<option value="'.$tmpLanguage->getLanguageId().'"';
			if ($tmpLanguage->getLanguageId() == $Language) {
				echo "selected";
			}
			echo '>'.$tmpLanguage->getName().'</option>';
        }
		?>			
		</SELECT>
	</TD>
</TR>
<!--<TR>
	<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cFrontPage" class="input_checkbox"></TD>
	<TD>
		<?php  putGS('Show article on front page'); ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><INPUT TYPE="CHECKBOX" NAME="cSectionPage" class="input_checkbox"></TD>
	<TD>
		<?php  putGS('Show article on section page'); ?>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"> <?php putGS("Enter keywords, comma separated");?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Keywords"); ?>:</TD>
	<TD>
		<INPUT TYPE="TEXT" NAME="cKeywords" SIZE="64" MAXLENGTH="255" class="input_text">
	</TD>
</TR>
-->
<TR>
	<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
		<!--<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='<?php  p($Back); ?>'">-->
		</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
</FORM>
<P>

<?php } ?>
<?php camp_html_copyright_notice(); ?>