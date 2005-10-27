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
	CampsiteInterface::DisplayError(getGS("You do not have the right to add sections."));
	exit;
}
if (!$User->hasPermission("AddArticle")) {
	CampsiteInterface::DisplayError(getGS("You do not have the right to add articles."));
	exit;
}

$SrcPubId = Input::Get('Pub', 'int', 0);
$SrcIssueId = Input::Get('Issue', 'int', 0);
$SrcSectionId = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$DestPublicationId = Input::Get('destination_publication', 'int', 0);
$DestIssueId = Input::Get('destination_issue', 'int', 0);
$DestIssueLanguageId = Input::Get('destination_issue_language', 'int', 0);;
$radioButton = Input::Get('section_chooser', 'string', 'new_section');
if ($radioButton == 'new_section') {
    $DestSectionId = Input::Get('destination_section_new_id', 'int', 0, true);
}
else {
    $DestSectionId = Input::Get('destination_section_existing', 'int', 0, true);    
}
$DestSectionName = Input::Get('destination_section_new_name', 'string', '', true);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/pub/issues/sections/index.php", true);

if (!Input::IsValid()) {
   	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}

$srcPublicationObj =& new Publication($SrcPubId);
if (!$srcPublicationObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Publication does not exist.'));
	exit;	
}

$srcIssueObj =& new Issue($SrcPubId, $Language, $SrcIssueId);
if (!$srcIssueObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Issue does not exist.'));
	exit;	
}

$srcSectionObj =& new Section($SrcPubId, $SrcIssueId, $Language, $SrcSectionId);
if (!$srcSectionObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Section does not exist.'));
	exit;	
}

$languageObj =& new Language($Language);

$correct = ($DestPublicationId > 0) && ($DestIssueId > 0) 
	    && ($DestIssueLanguageId > 0) && ($DestSectionId > 0);

if ($correct) {
    $dstSectionObj = $srcSectionObj->copy($DestPublicationId, $DestIssueId, $DestIssueLanguageId, 
                                           $DestSectionId);
    if (($radioButton == "new_section") && ($DestSectionName != "")) {
        $dstSectionObj->setName($DestSectionName);
    }
	$dstPublicationObj =& new Publication($DestPublicationId);
	$dstIssueObj =& new Issue($DestPublicationId, $DestIssueLanguageId, $DestIssueId);
	$created = true;
	// Record the event in the log.
    $logtext = getGS('Section $1 has been duplicated to $2. $3 of $4',
                     $dstSectionObj->getName(), $DestIssueId, $dstIssueObj->getName(),
                     $dstPublicationObj->getName());
    Log::Message($logtext, $User->getUserName(), 154);
                                           
	$topArray = array('Pub' => $srcPublicationObj, 'Issue' => $srcIssueObj, 'Section' => $dstSectionObj);
	CampsiteInterface::ContentTop(getGS('Duplicating section'), $topArray);
} else {
	$topArray = array('Pub' => $srcPublicationObj, 'Issue' => $srcIssueObj, 'Section' => $srcSectionObj);
	CampsiteInterface::ContentTop(getGS('Duplicating section'), $topArray);
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
		if ($created) { ?>	
		  <?php  putGS('Section $1 has been duplicated to $2. $3 of $4', '<B>'.$srcSectionObj->getName().'</B>', '<B>'.$dstSectionObj->getIssueId().'</B>', '<B>'.$dstIssueObj->getName().' ('.$dstIssueObj->getLanguageName().')</B>', '<B>'.$dstPublicationObj->getName().'</B>'); ?>
          <?php  
		} else { ?>	
		  <LI><?php  putGS('The section $1 could not be duplicated','<B>'.htmlspecialchars($srcSectionObj->getName()).'</B>'); ?></LI>
<?php  }
}
?>	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
            <?php  if ($created) { ?>
                <table>
                <tr>
                    <td>
	                   <b><a href="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($dstSectionObj->getPublicationId()); ?>&Issue=<?php  p($dstSectionObj->getIssueId()); ?>&Section=<?php  p($dstSectionObj->getSectionId()); ?>&Language=<?php p($dstSectionObj->getLanguageId()); ?>"><?php putGS("Go to new section"); ?></a></b>
	                </td>
	                <td style="padding-left: 50px;">
        	           <b><a href="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/?Pub=<?php  p($srcSectionObj->getPublicationId()); ?>&Issue=<?php  p($srcSectionObj->getIssueId()); ?>&Section=<?php  p($srcSectionObj->getSectionId()); ?>&Language=<?php p($srcSectionObj->getLanguageId()); ?>"><?php putGS("Go to source section"); ?></a></b>
        	        </td>
        	    </tr>
        	    </table>
                <?php  
            } else { ?>
	           <INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  p($SrcPubId); ?>&Issue=<?php  p($SrcIssueId); ?>&Language=<?php  p($Language); ?>'">
                <?php  
            } ?>
  		</DIV>
		</TD>
	</TR>
</TABLE></CENTER>
<P>

<?php CampsiteInterface::CopyrightNotice(); ?>

</HTML>
