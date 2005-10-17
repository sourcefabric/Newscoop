<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Alias.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to add publications."));
	exit;
}

$cName = trim(Input::Get('cName'));
$cSite = trim(Input::Get('cSite'));
$cLanguage = Input::Get('cLanguage', 'int');
$cURLType = Input::Get('cURLType', 'int', 0);

$cPayTime = Input::Get('cPayTime', 'int', 0, true);
$cTimeUnit = Input::Get('cTimeUnit', 'string', null, true);
$cUnitCost = Input::Get('cUnitCost', 'string', null, true);
$cCurrency = Input::Get('cCurrency', 'string', null, true);
$cPaid = Input::Get('cPaid', 'int', null, true);
$cTrial = Input::Get('cTrial', 'int', null, true);

$correct = true;
$created = false;
$errorMsgs = array();
	
if (empty($cName)) {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); 
}

if (empty($cSite)) {
	$correct = false;
	$errorMsgs[] = getGS('You must complete the $1 field.','<B>'.getGS('Site').'</B>'); 
}

if ($correct) {
	$aliases =& Alias::GetAliases(null, null, $cSite);
	if (count($aliases) <= 0) {
		$alias =& new Alias();
		$alias->create(array('Name' => $cSite));
		$newPub =& new Publication();
		$columns = array('Name' => $cName, 
						 'IdDefaultAlias'=> $alias->getId(), 
						 'IdDefaultLanguage' => $cLanguage,
						 'IdURLType' => $cURLType,
						 'PayTime' => $cPayTime,
						 'TimeUnit' => $cTimeUnit,
						 'UnitCost' => $cUnitCost,
						 'Currency' => $cCurrency,
						 'PaidTime' => $cPaid,
						 'TrialTime' => $cTrial);
		$created = $newPub->create($columns);
		if ($created) {
			$alias->setPublicationId($newPub->getPublicationId());
			$logtext = getGS('Publication $1 added', $cName); 
			Log::Message($logtext, $User->getUserName(), 1);
			header("Location: /$ADMIN/pub/edit.php?Pub=".$newPub->getPublicationId());
			exit;
		} else {
			$alias->delete();
			$errorMsgs[] = getGS('The publication could not be added.').' '.getGS('Please check if another publication with the same name or the same site name does not already exist.');
		}
	}
	else {
		$errorMsgs[] = getGS('The publication could not be added.').' '.getGS('Please check if another publication with the same name or the same site name does not already exist.');
	}
}

$crumbs = array();
$crumbs[] = array(getGS("Publications"), "/$ADMIN/pub");
$crumbs[] = array(getGS("Adding new publication"), "");
echo camp_html_breadcrumbs($crumbs);
?> 

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new publication"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php 
		foreach ($errorMsgs as $errorMsg) { ?>
			<LI><?php echo $errorMsg; ?></LI>
			<?php
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/pub/add.php'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>