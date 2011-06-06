<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/editor_load_countable.php");
require_once($GLOBALS['g_campsiteDir']. "/classes/ArticleType.php");

global $Campsite;

if (!$g_user->hasPermission('AddArticle')) {
	camp_html_display_error(getGS("You do not have the right to add articles."));
	exit;
}

// Only for use when in the article screens.
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

// The article location dropdowns cause this page to reload,
// so we need to preserve the state with each refresh.
$f_article_name = Input::Get('f_article_name', 'string', '', true);
$f_article_type = Input::Get('f_article_type', 'string', '', true);
$f_article_language = Input::Get('f_article_language', 'int', $f_language_id, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

// Only for the article screens.
$publicationObj = null;
$issueObj = null;
$sectionObj = null;
if ($f_publication_id > 0) {
	$publicationObj = new Publication($f_publication_id);
	if (($f_issue_number > 0) && ($f_article_language > 0)) {
		$issueObj = new Issue($f_publication_id, $f_article_language, $f_issue_number);
		if ($f_section_number > 0) {
			$sectionObj = new Section($f_publication_id, $f_issue_number, $f_article_language, $f_section_number);
		}
	}
}

// Only show the languages for sections which have been translated.
$sections = Section::GetSections($f_publication_id, $f_issue_number, null, null, null, null, true);
$languageIds = DbObjectArray::GetColumn($sections, 'IdLanguage');
$allLanguages = array();
foreach ($languageIds as $languageId) {
	if (!isset($allLanguages[$languageId])) {
		$allLanguages[$languageId] = new Language($languageId);
	}
}
$allArticleTypes = ArticleType::GetArticleTypes();

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
				  'Section' => $sectionObj);
camp_html_content_top(getGS('Add new article'), $topArray, true, false, array(getGS("Articles") => "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"));

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="<?php echo "/$ADMIN/articles/?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_section_number=$f_section_number&f_language_id=$f_language_id"; ?>"><B><?php  putGS("Article List"); ?></B></A></TD>
</TR>
</TABLE>

<?php
if (sizeof($allArticleTypes) == 0) {
?>
<p>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
	<td align="center" class="error_message">
	<?php putGS("No article types were defined. You must create an article type first."); ?>
	<p><b><a href="/<?php echo $ADMIN; ?>/article_types/"><?php putGS("Edit article types"); ?></a></b></p>
	</td>
</tr>
</table>
<?php
} else {
	include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");
	camp_html_display_msgs();
?>
<P>
<FORM NAME="add_article" METHOD="GET" ACTION="/<?php echo $ADMIN; ?>/articles/do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<?php if ($f_publication_id > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
<?php } ?>
<?php if ($f_issue_number > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($f_issue_number); ?>">
<?php } ?>
<?php if ($f_section_number > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php p($f_section_number); ?>">
<?php } ?>
<?php if ($f_language_id > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
<?php } ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="box_table">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Add new article"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<td valign="top">
		<table>
		<tr>
			<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
			<TD>
			<INPUT TYPE="TEXT" NAME="f_article_name" SIZE="40" MAXLENGTH="140" class="input_text countable" alt="blank" emsg="<?php putGS('You must fill in the $1 field.', getGS('Name')); ?>" value="<?php echo htmlspecialchars($f_article_name); ?>">
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
			<TD>
			    <?php if (count($allArticleTypes) == 1) { ?>
			        <INPUT TYPE="HIDDEN" NAME="f_article_type" VALUE="<?php echo $allArticleTypes[0]; ?>">
                    <?php
                        $tmpAT = new ArticleType($allArticleTypes[0]);
                        echo $tmpAT->getDisplayName();
			    } else { ?>
    				<SELECT NAME="f_article_type" class="input_select" alt="select" emsg="<?php putGS('You must fill in the $1 field.', getGS('Article Type')); ?>">
	   		      	<option></option>
		  		    <?php
    				foreach ($allArticleTypes as $tmpType) {
	       			    $tmpAT = new ArticleType($tmpType);
			     	    camp_html_select_option($tmpType, $f_article_type, $tmpAT->getDisplayName());
				    }
					?>
				    </SELECT>
               <?php } ?>
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
			<TD style="padding-top: 3px;">
				<?php if (count($allLanguages) > 1) { ?>
				<SELECT NAME="f_article_language" alt="select" emsg="<?php putGS("You must select a language.")?>" class="input_select">
				<option value="0"><?php putGS("---Select language---"); ?></option>
				<?php
			 	foreach ($allLanguages as $tmpLanguage) {
			 		camp_html_select_option($tmpLanguage->getLanguageId(),
			 								$f_article_language,
			 								$tmpLanguage->getNativeName());
		        }
				?>
				</SELECT>
				<?php } else {
					$tmpLanguage = array_pop($allLanguages);
					echo '<b>'.htmlspecialchars($tmpLanguage->getNativeName()).'</b>';
					?>
					<input type="hidden" name="f_article_language" value="<?php p($tmpLanguage->getLanguageId()); ?>">
					<?php
				}
				?>

			</TD>
		</TR>
		</table>
	</td>
</tr>
<TR>
	<TD COLSPAN="2" align="center">
		<HR NOSHADE SIZE="1" COLOR="BLACK">
		<INPUT TYPE="submit" NAME="save" VALUE="<?php  putGS('Save'); ?>" class="button">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.add_article.f_article_name.focus();
</script>
<?php } ?>
<?php camp_html_copyright_notice(); ?>
