<?php

check_basic_access($_REQUEST);
if (!isset($editUser) || gettype($editUser) != 'object') {
	CampsiteInterface::DisplayError('No such user account.');
	exit;
}
list($access, $User) = check_basic_access($_REQUEST);
compute_user_rights($User, &$canManage, &$canDelete);
if (!$canManage) {
	if ($editUser->getUserName() == '')
		$error = "You do not have the right to create user accounts.";
	else
		$error = 'You do not have the right to change user account information.';
	CampsiteInterface::DisplayError($error);
	exit;
}

$fields = array('UName', 'Name', 'Title', 'Gender', 'Age', 'EMail', 'City', 'StrAddress',
	'State', 'CountryCode', 'Phone', 'Fax', 'Contact', 'Phone2', 'PostalCode', 'Employer',
	'EmployerType', 'Position');
if ($editUser->getUserName() == '') {
	$action = 'do_add.php';
	foreach ($fields as $index=>$field)
		$$field = Input::Get($field, 'string', '');
} else {
	$action = 'do_info.php';
	foreach ($fields as $index=>$field)
		$$field = $editUser->getProperty($field);
}

?>
<form name="dialog" method="POST" action="<?php echo $action; ?>">
<input type="hidden" name="uType" value="<?php echo $uType; ?>">
<?php if ($editUser->getUserName() != '') { ?>
<input type="hidden" name="User" value="<?php echo $editUser->getId(); ?>">
<?php } ?>
<table border="0" cellspacing="0" cellpadding="6" class="table_input" align="center">
	<tr>
		<td align="right" nowrap><?php putGS("User name"); ?>:</td>
<?php if ($editUser->getUserName() != '') { ?>
		<td align="left" nowrap><b><?php pencHTML($editUser->getUserName()); ?></b></td>
<?php } else { ?>
		<td><input type="text" class="input_text" name="UName" size="32" maxlength="32" value="<?php pencHTML($UName); ?>"></td>
	</tr>
	<tr>
		<td align="right"><?php putGS("Password"); ?>:</td>
		<td>
		<input type="password" class="input_text" name="password" size="16" maxlength="32">
		</td>
	</tr>
	<tr>
		<td align="right"><?php putGS("Confirm password"); ?>:</td>
		<td>
		<input type="password" class="input_text" name="passwordConf" size="16" maxlength="32">
		</td>
<?php } ?>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Full Name"); ?>:</td>
		<td><input type="text" class="input_text" name="Name" VALUE="<?php pencHTML($Name); ?>" size="32" maxlength="128">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Title"); ?>:</td>
		<td>
<?php
	CampsiteInterface::CreateSelect("Title", array("Mr.", "Mrs.", "Ms.", "Dr."),
		$Title, 'class="input_select"');
?>
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Gender"); ?>:</td>
		<td>
		<input type=radio name=Gender value="M"<?php if($Gender == "M") { ?> CHECKED<?php  } ?>><?php  putGS('Male'); ?>
		<input type=radio name=gender value="F"<?php if($Gender == "F") { ?> CHECKED<?php  } ?>><?php  putGS('Female'); ?>
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Age"); ?>:</td>
		<td>
<?php
	CampsiteInterface::CreateSelect("Age", array("0-17"=>getGS("under 18"),
			"18-24"=>"18-24", "25-39"=>"25-39", "40-49"=>"40-49", "50-65"=>"50-65",
			"65-"=>getGS('65 or over')),
		$Age, 'class="input_select"', true);
?>
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("E-Mail"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="EMail" value="<?php pencHTML($EMail); ?>" size="32" maxlength="128">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("City"); ?>:</td>
		<td>
		<input type="text" class="input_text" NAME="City" VALUE="<?php pencHTML($City); ?>" size="32" maxlength="60">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Street Address"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="StrAddress" value="<?php  pencHTML($StrAddress); ?>" size="40" maxlength="255">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Postal Code"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="PostalCode" value="<?php pencHTML($PostalCode); ?>" size="10" maxlength="10">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("State"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="State" value="<?php pencHTML($State); ?>" size="32" maxlength="32">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Country"); ?>:</td>
		<td>
<?php
	$countries_list[""] = "";
	query ("SELECT Code, Name FROM Countries where IdLanguage = 1", 'countries');
	for($loop = 0; $loop < $NUM_ROWS; $loop++) {
		fetchRow($countries);
		$countries_list[getHVar($countries,'Code')] = getHVar($countries,'Name');
	}
	CampsiteInterface::CreateSelect("CountryCode", $countries_list,
		$CountryCode, 'class="input_select"', true);
?>
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Phone"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="Phone" value="<?php pencHTML($Phone); ?>" size="20" maxlength="20">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Fax"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="Fax" value="<?php pencHTML($Fax); ?>" size="20" maxlength="20">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Contact Person"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="Contact" value="<?php  pencHTML($Contact); ?>" size="32" maxlength="64">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Second Phone"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="Phone2" value="<?php  pencHTML($Phone2); ?>" size="20" maxlength="20">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Employer"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="Employer" value="<?php  pencHTML($Employer); ?>" size="30" maxlength="30">
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Employer Type"); ?>:</td>
		<td>
<?php
	$employerTypes[''] = '';
	$employerTypes['Corporate'] = getGS('Corporate');
	$employerTypes['NGO'] = getGS('Non-Governmental Organisation');
	$employerTypes['Government Agency'] = getGS('Government Agency');
	$employerTypes['Academic'] = getGS('Academic');
	$employerTypes['Media'] = getGS('Media');
	CampsiteInterface::CreateSelect("EmployerType", $employerTypes,
		$EmployerType, 'class="input_select"', true);
?>
		</td>
	</tr>
	<tr>
		<td align="right" nowrap><?php putGS("Position"); ?>:</td>
		<td>
		<input type="text" class="input_text" name="Position" value="<?php pencHTML($Position); ?>" size="30" maxlength="30">
		</td>
	</tr>
<?php if ($UName == '' && $uType == "Staff") { ?>
	<tr>
		<td align="right"><?php putGS("Type"); ?>:</td>
		<td>
<?php query ("SELECT Name FROM UserTypes WHERE Reader = 'N' ORDER BY Name ASC", 'q'); ?>
		<select name="Type" class="input_select">
<?php
	$Type = Input::Get('Type', 'string', '');
	$nr = $NUM_ROWS;
	for($loop = 0; $loop < $nr; $loop++) {
		fetchRow($q);
?>
			<option <?php if ($Type == getHVar($q,'Name')) { ?>selected<?php } ?>>
<?php
		pgetHVar($q,'Name');
	}
?>
		</select>
		</td>
	</tr>
<?php
} else {
	echo "<input type=\"hidden\" name=\"Type\" value=\"$uType\">\n";
}
?>
	<tr>
		<td colspan="2">
		<div align="center">
		<input type="submit" class="button" name="Save" value="<?php  putGS('Save changes'); ?>">
		<input type="button" class="button" name="Cancel" value="<?php putGS('Cancel'); ?>" onclick="location.href='<?php echo "/$ADMIN/users/?" . get_user_urlparams(); ?>'">
		</div>
		</td>
	</tr>
</table>
</form>
