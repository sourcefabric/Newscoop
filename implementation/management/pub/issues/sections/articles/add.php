<?php 
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddArticle')) {
	CampsiteInterface::DisplayError("You do not have the right to add articles.");
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
	CampsiteInterface::DisplayError(array('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$languageObj =& new Language($Language);

$allArticleTypes = ArticleType::GetAllTypes();
$allLanguages = Language::GetAllLanguages();

## added by sebastian
if (function_exists ("incModFile")) {
	incModFile ();
}
?>
<HEAD>
	<TITLE><?php  putGS("Add new article"); ?></TITLE>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">
</HEAD>

<BODY>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
<TR>
	<TD class="page_title">
	    <?php  putGS("Add new article"); ?>
	</TD>
	<TD ALIGN="RIGHT" valign="middle">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0" >
		<TR>
			<?php if ($Wiz == 0) { ?>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" class="breadcrumb"><?php  putGS("Articles");  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<?php  } ?>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" class="breadcrumb"><?php  putGS("Sections");  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>?Pub=<?php  p($Pub); ?>" class="breadcrumb"><?php  putGS("Issues");  ?></A></TD>
			<td class="breadcrumb_separator">&nbsp;</td>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>" class="breadcrumb"><?php  putGS("Publications");  ?></A></TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php p(htmlspecialchars($publicationObj->getName())); ?></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Issue"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php p($issueObj->getIssueId()); ?>. <?php  p(htmlspecialchars($issueObj->getName())); ?> (<?php p(htmlspecialchars($languageObj->getName())); ?>)</TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Section"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php p($sectionObj->getSectionId()); ?>. <?php  p(htmlspecialchars($sectionObj->getName())); ?></TD>
</TR>
</TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php" >
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
	<INPUT TYPE="TEXT" NAME="cName" SIZE="64" MAXLENGTH="140" class="input_text">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Type"); ?>:</TD>
	<TD>
		<SELECT NAME="cType" class="input_select">
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
<TR>
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
<TR>
	<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='<?php  p($Back); ?>'">
		</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
</FORM>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>