<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/pub/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Template.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageIssue')) {
	CampsiteInterface::DisplayError('You do not have the right to change issue details.');
	exit;
}
$Pub = Input::Get('Pub', 'int');
$Issue = Input::Get('Issue', 'int');
$Language = Input::Get('Language', 'int');
$cName = trim(Input::Get('cName'));
$cLang = Input::Get('cLang', 'int');
$cPublicationDate = Input::Get('cPublicationDate', 'string', '', true);
$cIssueTplId = Input::Get('cIssueTplId', 'int');
$cSectionTplId = Input::Get('cSectionTplId', 'int');
$cArticleTplId = Input::Get('cArticleTplId', 'int');
$cShortName = trim(Input::Get('cShortName'));

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(array('Invalid input: $1', Input::GetErrorString()));	
	exit;
}
$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);

$created = 0;
$correct = true;
if ($cLang == 0) {
	$correct = false;
}
if ($cName == '') {
	$correct = false;
}
if ($cShortName == '') {
	$correct = false;
}
if (!valid_short_name($cShortName)) {
	$correct = false;
}
if ($correct) {
	$issueObj->setProperty('Name', $cName, false);
	$issueObj->setProperty('IdLanguage', $cLang, false);
	if ($issueObj->getPublished() == 'Y') {
		$issueObj->setProperty('PublicationDate', $cPublicationDate, false);
	}
	$issueObj->setProperty('IssueTplId', $cIssueTplId, false);
	$issueObj->setProperty('SectionTplId', $cSectionTplId, false);
	$issueObj->setProperty('ArticleTplId', $cArticleTplId, false);
	$issueObj->setProperty('ShortName', $cShortName, false);
	$created = $issueObj->commit();
	if ($created) {
		$logtext = getGS('Issue $1 updated in publication $2', $cName, $publicationObj->getName());
		Log::Message($logtext, $User->getUserName(), 11);
	}
}

CampsiteInterface::ContentTop('Updating issue', array('Pub' => $publicationObj, 'Issue' => $issueObj));

?>
<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Changing issue's details"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php 
		if ($cLang == 0) {	?>
			<LI><?php  putGS('You must select a language.'); ?></LI>
			<?php
		}
		if ($cName == '') {
			echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>') . "</LI>\n";
		}
		if ($cShortName == '') {
			echo "<LI>" . getGS('You must complete the $1 field.','<B>'.getGS('URL Name').'</B>') . "</LI>\n";
		}
		if (!valid_short_name($cShortName)) {
			echo "<LI>" . getGS('The $1 field may only contain letters, digits and underscore (_) character.', '</B>' . getGS('URL Name') . '</B>') . "</LI>\n";
		}
		if ($created) {
?>			<LI><?php  putGS('The issue $1 has been successfuly changed.', '<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
			<?php
		} else {
			if ($correct != 0) { ?>
				<LI><?php  putGS('The issue could not be changed.'); ?></LI>
				<?php
			}
		}
		?>	
		</BLOCKQUOTE>
	</TD>
</TR>

<?php  if ($correct && $created) { ?>	
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  putGS('Done'); ?>" ONCLICK="location.href='/admin/pub/issues/?Pub=<?php  pencURL($Pub); ?>'">
	</DIV>
	</TD>
</TR>
<?php  } else { ?>	
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/admin/pub/issues/edit.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pencURL($Issue); ?>&Language=<?php  pencURL($Language); ?>'">
	</DIV>
	</TD>
</TR>
<?php  } ?>
</TABLE>
</CENTER>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>