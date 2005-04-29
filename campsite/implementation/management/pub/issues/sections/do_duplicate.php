<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/section_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}
if (!$User->hasPermission("ManageSection")) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to add sections." )));
	exit;
}
if (!$User->hasPermission("AddArticle")) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS("You do not have the right to add articles." )));
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$DestPublication = Input::Get('destination_publication', 'int', 0, true);
$DestIssue = Input::Get('destination_issue', 'int', 0, true);
$DestSection = Input::Get('destination_section', 'int', 0, true);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/index.php", true);

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Publication does not exist.')));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Issue does not exist.')));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	header("Location: /$ADMIN/ad.php?ADReason=".urlencode(getGS('Section does not exist.')));
	exit;	
}

$languageObj =& new Language($Language);

$allPublications =& Publication::GetAllPublications();
$allIssues = array();
if ($DestPublication > 0) {
	$allIssues =& Issue::GetIssuesInPublication($DestPublication);
}

$correct = ($Language > 0) && ($Pub > 0) && ($Issue > 0) && ($Section > 0)
	&& ($DestPublication > 0) && ($DestIssue > 0) && ($DestSection > 0);

if ($correct) {
	$dstPublicationObj =& new Publication($DestPublication);
	$dstIssueObj =& new Issue($DestPublication, $Language, $DestIssue);
	$dstSectionObj =& new Section($DestPublication, $DestIssue, $Language, $DestSection);
	if ($Pub == $DestPublication && $Issue == $DestIssue) {
		$shortName = $DestSection;
		$sectionName = $sectionObj->getName() . " (duplicate)";
	} else {
		$shortName = $sectionObj->getShortName();
		$sectionName = $sectionObj->getName();
	}
	$dstSectionCols = array('Name'=>$sectionName, 'ShortName'=>$shortName);
	if ($sectionObj->getProperty('SectionTplId') != "")
		$dstSectionCols['SectionTplId'] = $sectionObj->getProperty('SectionTplId');
	if ($sectionObj->getProperty('ArticleTplId') != "")
		$dstSectionCols['ArticleTplId'] = $sectionObj->getProperty('ArticleTplId');
	if ($dstSectionObj->exists()) {
		$dstSectionObj->update($dstSectionCols);
	} else {
		$dstSectionObj->create($dstSectionCols);
	}
	$sectionArticles = Article::GetArticles($Pub, $Issue, $Section, $Language);
	foreach ($sectionArticles as $index=>$articleObj) {
		$articleCopy = $articleObj->copy($DestPublication, $DestIssue, $DestSection, $User->getId());
	}
	$logtext = getGS('Section $1 has been duplicated to $2. $3 of $4',
		$sectionObj->getName(), $DestIssue, $dstIssueObj->getName(),
		$dstPublicationObj->getName());
	Log::Message($logtext, $User->getUserName(), 154);
	$created = true;
	SectionTop($dstSectionObj, $Language, "Duplicating section");
} else {
	SectionTop($sectionObj, $Language, "Duplicating section");
}

?>
<P>
<CENTER><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Duplicating section"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php 
	if (!$correct) {
		echo "<LI>"; putGS('Invalid parameters received'); echo "</LI>\n";
	} else {
		if ($created) { ?>	<LI><?php  putGS('Section $1 has been duplicated to $2. $3 of $4', '<B>'.$sectionObj->getName().'</B>', '<B>'.$DestIssue.'</B>', '<B>'.$dstIssueObj->getName().'</B>', '<B>'.$dstPublicationObj->getName().'</B>'); ?></LI>
<?php  } else { ?>	<LI><?php  putGS('The section $1 could not be duplicated','<B>'.encHTML(decS($sect_name)).'</B>'); ?></LI>
<?php  }
}
?>	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
<?php  if ($created) { ?>
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($DestPublication); ?>&Issue=<?php  p($DestIssue); ?>&Section=<?php  p($DestSection); ?>&Language=<?php  p($Language); ?>'">
<?php  } else { ?>
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Language=<?php  p($Language); ?>'">
<?php  } ?>		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>

</HTML>
