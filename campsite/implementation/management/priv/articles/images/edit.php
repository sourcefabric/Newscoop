<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/articles/images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$ImageId = Input::Get('ImageId', 'int', 0);
$ImageTemplateId = Input::Get('ImageTemplateId', 'int', 0, true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$publicationObj =& new Publication($Pub);
$issueObj =& new Issue($Pub, $Language, $Issue);
$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
$articleObj =& new Article($Pub, $Issue, $Section, $sLanguage, $Article);
$imageObj =& new Image($ImageId);

// Add extra breadcrumb for image list.
$extraCrumbs = array(getGS("Images")=>"/$ADMIN/articles/images/?Pub=$Pub&Issue=$Issue&Language=$Language&Section=$Section&Article=$Article&sLanguage=$sLanguage");
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
CampsiteInterface::ContentTop(getGS('Change image information'), $topArray, true, true, $extraCrumbs);
?>
<P>
<CENTER>
<IMG SRC="<?php echo $imageObj->getImageUrl(); ?>" BORDER="0" ALT="<?php echo htmlspecialchars($imageObj->getDescription()); ?>">
</CENTER>
<?php if ($User->hasPermission('ChangeImage')) { ?>
<p>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php" >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
	<TR>
		<TD COLSPAN="2">
			<B><?php  putGS('Change image information'); ?></B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<?php if ($ImageTemplateId > 0) { ?>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Number'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cNumber" VALUE="<?php echo $ImageTemplateId; ?>" class="input_text" SIZE="32" MAXLENGTH="10">
		</TD>
	</TR>
	<?php } ?>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Description'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cDescription" VALUE="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" class="input_text" SIZE="32" MAXLENGTH="128">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Photographer'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cPhotographer" VALUE="<?php echo htmlspecialchars($imageObj->getPhotographer());?>" class="input_text" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Place'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cPlace" VALUE="<?php echo htmlspecialchars($imageObj->getPlace()); ?>" class="input_text" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" ><?php  putGS('Date'); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" NAME="cDate" VALUE="<?php echo htmlspecialchars($imageObj->getDate()); ?>" class="input_text" SIZE="11" MAXLENGTH="10"> <?php putGS('YYYY-MM-DD'); ?>
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
	    <INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
	    <INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
	    <INPUT TYPE="HIDDEN" NAME="Image" VALUE="<?php  p($ImageId); ?>">
		<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>" class="button">
		<INPUT TYPE="button" NAME="Cancel" VALUE="<?php  putGS('Cancel'); ?>" class="button" ONCLICK="location.href='/<?php echo $ADMIN; ?>/articles/images/?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&Section=<?php  p($Section); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
</FORM>
<P>
<?php 
}

CampsiteInterface::CopyrightNotice(); ?>