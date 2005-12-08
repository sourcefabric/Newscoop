<?php

require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/pub_common.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/SubscriptionDefaultTime.php");

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManagePub')) {
	camp_html_display_error(getGS("You do not have the right to manage publications."));
	exit;
}

$cPub = Input::Get('cPub', 'int');
$Language = Input::Get('Language', 'int', 1, true);
$cCountryCode = trim(Input::Get('cCountryCode'));
$cTrialTime = Input::Get('cTrialTime', 'int', 0);
$cPaidTime = Input::Get('cPaidTime', 'int', 0);
$correct = true;
$created = false;
$publicationObj =& new Publication($cPub);

if (empty($cCountryCode)) {
	$correct = false; 
	$errorMsgs[] = getGS('You must select a country.'); 
}
    
if ($correct) {
	$defaultTime = new SubscriptionDefaultTime($cCountryCode, $cPub);
	$created = $defaultTime->create(array('TrialTime' => $cTrialTime, 'PaidTime' => $cPaidTime));
	if ($created) {
		header("Location: /$ADMIN/pub/editdeftime.php?Pub=$cPub&CountryCode=$cCountryCode&Language=$Language");
		exit;
	}
} else {
    $errorMsgs[] = getGS('The default subscription time for country $1 could not be added.', $publicationObj->getName().':'.$cCountryCode) .' '.getGS('Please check if another entry with the same country code exists already.'); 
}

$crumbs = array(getGS("Subscriptions") => "deftime.php?Pub=$cPub");
camp_html_content_top(getGS("Adding new country default subscription time"), array("Pub" => $publicationObj), true, false, $crumbs);

    
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
<TR>
	<TD COLSPAN="2">
		<B> <?php  putGS("Adding new country default subscription time"); ?> </B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
		<BLOCKQUOTE>
		<?php 
		foreach ($errorMsgs as $errorMsg) { ?>
			<li><?php p($errorMsg); ?></li>
			<?php
		}
		?>
		</BLOCKQUOTE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/pub/countryadd.php?Pub=<?php p($cPub); ?>&Language=<?php p($Language); ?>'">
	</DIV>
	</TD>
</TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>
