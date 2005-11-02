<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/TimeUnit.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/UrlType.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Language.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to change publication information."));
	exit;
}

$Pub = Input::Get('Pub', 'int');
$TOL_Language = Input::Get('TOL_Language');
$cName = trim(Input::Get('cName'));
$cDefaultAlias = Input::Get('cDefaultAlias', 'int');
$cLanguage = Input::Get('cLanguage', 'int');
$cURLType = Input::Get('cURLType', 'int');
$cPayTime = Input::Get('cPayTime', 'int');
$cTimeUnit = Input::Get('cTimeUnit');
$cUnitCost = trim(Input::Get('cUnitCost', 'float', '0.0'));
$cCurrency = trim(Input::Get('cCurrency'));
$cPaid = Input::Get('cPaid', 'int');
$cTrial = Input::get('cTrial', 'int');
$errorMsgs = array();
$correct = true;
$updated = false;
if (empty($cName)) {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); 
}
if (empty($cDefaultAlias)) {
	$correct = false;
	$errorMsgs = getGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>'); 
}

$publicationObj =& new Publication($Pub);
if ($correct) {
	$columns = array('Name' => $cName,
					 'IdDefaultAlias' => $cDefaultAlias,
					 'IdDefaultLanguage' => $cLanguage,
					 'IdURLType' => $cURLType,
					 'PayTime' => $cPayTime,
					 'TimeUnit' => $cTimeUnit,
					 'PaidTime' => $cPaid,
					 'TrialTime' => $cTrial,
					 'UnitCost' => $cUnitCost,
					 'Currency' => $cCurrency);
	$updated = $publicationObj->update($columns);
//	if ($updated) {
		$logtext = getGS('Publication $1 changed', $publicationObj->getName()); 
		Log::Message($logtext, $User->getUserName(), 3);
		header("Location: /$ADMIN/pub/edit.php?Pub=$Pub");
		exit;
//	} 
//	else {
//		$errorMsgs[] = getGS('The publication information could not be updated.')
//					  .' '.getGS('Please check if another publication with the same or the same site name does not already exist.'); 
//	}
}

echo camp_html_content_top(getGS("Changing publication information"), array("Pub" => $publicationObj));
?>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Changing publication information"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php 
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?> </li>
			<?php
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" align="center">
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/edit.php?Pub=<?php  p($Pub); ?>'">
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
