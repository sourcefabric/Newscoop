<?php 
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission('AddArticle')) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to add articles." )));
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$Issue = Input::get('Issue', 'int', 0);
$Section = Input::get('Section', 'int', 0);
$Language = Input::get('Language', 'int', 0);
$Back = Input::get('Back', 'string', 'index.php', true);
$Wiz = Input::get('Wiz', 'int', 0, true);
if ($Wiz != 0) {
	$Back = "/$ADMIN/home.php";
}

if (!Input::isValid()) {
	header("Location: /$ADMIN/logout.php");
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
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite["website_url"] ?>/css/admin_stylesheet.css">
</HEAD>

<BODY  BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%">
<TR>
	<!--<TD ROWSPAN="2" WIDTH="1%"><IMG SRC="/<?php echo $ADMIN; ?>/img/sign_big.gif" BORDER="0"></TD>-->
	<TD style="padding-left: 10px; padding-top: 10px;">
	    <DIV STYLE="font-size: 12pt"><B><?php  putGS("Add new article"); ?></B></DIV>
	    <!--<HR NOSHADE SIZE="1" COLOR="BLACK">-->
	</TD>
<!--</TR>
<TR>
-->	<TD ALIGN="RIGHT" style="padding-right: 10px; padding-top: 10px;">
		<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
		<TR>
			<?php if ($Wiz == 0) { ?>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Articles"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>&Section=<?php  p($Section); ?>" ><B><?php  putGS("Articles");  ?></B></A></TD>
			<?php  } ?>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Sections"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>" ><B><?php  putGS("Sections");  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>?Pub=<?php  p($Pub); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Issues"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/issues/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>?Pub=<?php  p($Pub); ?>" ><B><?php  putGS("Issues");  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Publications"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/pub/<?php  if ($Wiz) { ?>add_article.php<?php  } ?>" ><B><?php  putGS("Publications");  ?></B></A></TD>
			<!--<TD><A HREF="/<?php echo $ADMIN; ?>/home.php" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Home"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/home.php" ><B><?php  putGS("Home");  ?></B></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/logout.php" ><IMG SRC="/<?php echo $ADMIN; ?>/img/tol.gif" BORDER="0" ALT="<?php  putGS("Logout"); ?>"></A></TD>
			<TD><A HREF="/<?php echo $ADMIN; ?>/logout.php" ><B><?php  putGS("Logout");  ?></B></A></TD>-->
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
<HR NOSHADE SIZE="1" COLOR="BLACK">

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Publication"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php p(htmlspecialchars($publicationObj->getName())); ?></B></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Issue"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php p($issueObj->getIssueId()); ?>. <?php  p(htmlspecialchars($issueObj->getName())); ?> (<?php p(htmlspecialchars($languageObj->getName())); ?>)</B></TD>

	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP">&nbsp;<?php  putGS("Section"); ?>:</TD>
	<TD BGCOLOR="#D0D0B0" VALIGN="TOP"><B><?php p($sectionObj->getSectionId()); ?>. <?php  p(htmlspecialchars($sectionObj->getName())); ?></B></TD>
</TR>
</TABLE>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_add.php" >
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" BGCOLOR="#C0D0FF" ALIGN="CENTER">
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