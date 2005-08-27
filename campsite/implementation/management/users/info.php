<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/classes/UserType.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!isset($editUser) || gettype($editUser) != 'object') {
	CampsiteInterface::DisplayError(getGS('No such user account.'));
	exit;
}
$isNewUser = $editUser->getUserName() == '';
compute_user_rights($User, $canManage, $canDelete);
if (!$canManage && $editUser->getId() != $User->getId()) {
	if ($isNewUser) {
		$error = getGS("You do not have the right to create user accounts.");
	} else {
		$error = getGS('You do not have the right to change user account information.');
	}
	CampsiteInterface::DisplayError($error);
	exit;
}

$fields = array('UName', 'Name', 'Title', 'Gender', 'Age', 'EMail', 'City', 'StrAddress',
	'State', 'CountryCode', 'Phone', 'Fax', 'Contact', 'Phone2', 'PostalCode', 'Employer',
	'EmployerType', 'Position');
if ($isNewUser) {
	$action = 'do_add.php';
	foreach ($fields as $index=>$field)
		$$field = Input::Get($field, 'string', '');
} else {
	$action = 'do_edit.php';
	foreach ($fields as $index=>$field)
		$$field = $editUser->getProperty($field);
}

?>
<script>
function ToggleRowVisibility(id) {
	if (document.getElementById(id).style.display == "none") {
		if (document.all) {
			document.getElementById(id).style.display = "block";
		}
		else {
			document.getElementById(id).style.display = "";
		}
	}
	else {
		document.getElementById(id).style.display = "none";
	}
}
function ToggleBoolValue(element_id) {
    if (document.getElementById(element_id).value == "false") {
		document.getElementById(element_id).value = "true";
    }
    else {
	   document.getElementById(element_id).value = "false";
    }
}
</script>

<form name="dialog" method="POST" action="<?php echo $action; ?>" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<input type="hidden" name="uType" value="<?php echo $uType; ?>">
<?php
if (!$isNewUser) { 
?>
<input type="hidden" name="User" value="<?php echo $editUser->getId(); ?>">
<?php
}
?>
<table border="0" cellspacing="0" align="center" class="table_input">
<tr>
	<td>
		<table border="0" cellspacing="0" cellpadding="3" align="center" width="100%">
			<tr>
				<td align="right" nowrap><?php putGS("User name"); ?>:</td>
<?php
if (!$isNewUser) {
?>
				<td align="left" nowrap><b><?php pencHTML($editUser->getUserName()); ?></b></td>
<?php
} else {
?>
				<td><input type="text" class="input_text" name="UName" size="32" maxlength="32" value="<?php pencHTML($UName); ?>" alt="blank" emsg="<?php putGS("You must complete the $1 field.", "User name"); ?>"></td>
			</tr>
			<tr>
				<td align="right"><?php putGS("Password"); ?>:</td>
				<td>
				<input type="password" class="input_text" name="password" size="16" maxlength="32" alt="length|6" emsg="<?php putGS("The password must be at least 6 characters long and both passwords should match."); ?>">
				</td>
			</tr>
			<tr>
				<td align="right"><?php putGS("Confirm password"); ?>:</td>
				<td>
				<input type="password" class="input_text" name="passwordConf" size="16" maxlength="32" alt="length|6" emsg="<?php putGS("The confirm password must be at least 6 characters long and both passwords should match."); ?>">
				</td>
<?php
}
?>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Full Name"); ?>:</td>
				<td><input type="text" class="input_text" name="Name" VALUE="<?php pencHTML($Name); ?>" size="32" maxlength="128" alt="blank" emsg="<?php putGS("You must complete the $1 field.", "Full Name");?>">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("E-Mail"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="EMail" value="<?php pencHTML($EMail); ?>" size="32" maxlength="128" alt="email" emsg="<?php putGS("You must input a valid EMail address.");?>">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Phone"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Phone" value="<?php pencHTML($Phone); ?>" size="20" maxlength="20">
				</td>
			</tr>
<?php
if ($isNewUser && $uType == "Staff") {
?>
			<tr>
				<td align="right"><?php putGS("Type"); ?>:</td>
				<td>
<?php
	query ("SELECT Name FROM UserTypes WHERE Reader = 'N' ORDER BY Name ASC", 'q');
?>
				<select name="Type" class="input_select" alt="select" emsg="<?php putGS("You must select a $1", "Type"); ?>">
				<option value=""><?php putGS("Make a selection"); ?></option>
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
		</table>
	</td>
</tr>
<?php
if (!$isNewUser) {
?>
<input type="hidden" name="setPassword" id="set_password" value="false">
<tr id="password_show_link">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('password_dialog'); ToggleRowVisibility('password_hide_link'); ToggleRowVisibility('password_show_link'); ToggleBoolValue('set_password');">
			<img src="/admin/img/icon/viewmag+.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to change password"); ?>
		</a>
	</td>
</tr>
<tr id="password_hide_link" style="display: none;">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('password_dialog'); ToggleRowVisibility('password_hide_link'); ToggleRowVisibility('password_show_link'); ToggleBoolValue('set_password');">
			<img src="/admin/img/icon/viewmag-.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to leave password unchanged"); ?>
		</a>
	</td>
</tr>
<tr id="password_dialog" style="display: none;">
	<td>
		<table border="0" cellspacing="0" cellpadding="3" align="center" width="100%">
<?php
	if ($userId == $User->getId() && !$isNewUser) {
?>
		<tr>
			<td align="right" nowrap width="1%"><?php putGS("Old Password"); ?>:</td>
			<td>
			<input type="password" class="input_text" name="oldPassword" size="16" maxlength="32">
			</td>
		</tr>
<?php
	}
?>
		<tr>
			<td align="right" nowrap width="1%"><?php putGS("Password"); ?>:</td>
			<td>
			<input type="password" class="input_text" name="password" size="16" maxlength="32">
			</td>
		</tr>
		<tr>
			<td align="right" nowrap width="1%"><?php putGS("Confirm password"); ?>:</td>
			<td>
			<input type="password" class="input_text" name="passwordConf" size="16" maxlength="32">
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
} // if ($isNewUser)
?>
<tr id="user_details_show_link">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('user_details_dialog'); ToggleRowVisibility('user_details_hide_link'); ToggleRowVisibility('user_details_show_link');">
			<img src="/admin/img/icon/viewmag+.png" id="my_icon" border="0" align="center">
			<?php putGS("Show more user details"); ?>
		</a>
	</td>
</tr>
<tr id="user_details_hide_link" style="display: none;">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('user_details_dialog'); ToggleRowVisibility('user_details_hide_link'); ToggleRowVisibility('user_details_show_link');">
			<img src="/admin/img/icon/viewmag-.png" id="my_icon" border="0" align="center">
			<?php putGS("Hide user details"); ?>
		</a>
	</td>
</tr>
<tr id="user_details_dialog" style="display: none;">
	<td>
		<table border="0" cellspacing="0" cellpadding="3" align="center" width="100%">
			<tr>
				<td align="right" nowrap><?php putGS("Title"); ?>:</td>
				<td>
<?php
CampsiteInterface::CreateSelect("Title", array(getGS("Mr."), getGS("Mrs."), getGS("Ms."), getGS("Dr.")),
	$Title, 'class="input_select"');
?>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Gender"); ?>:</td>
				<td>
				<input type=radio name=Gender value="M"<?php if($Gender == "M") { ?> CHECKED<?php  } ?>><?php  putGS('Male'); ?>
				<input type=radio name=Gender value="F"<?php if($Gender == "F") { ?> CHECKED<?php  } ?>><?php  putGS('Female'); ?>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Age"); ?>:</td>
				<td>
<?php
CampsiteInterface::CreateSelect("Age", array("0-17"=>getGS("under 18"),
	"18-24"=>"18-24", "25-39"=>"25-39", "40-49"=>"40-49", "50-65"=>"50-65",
	"65-"=>getGS('65 or over')), $Age, 'class="input_select"', true);
?>
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
				<input type="text" class="input_text" name="StrAddress" value="<?php  pencHTML($StrAddress); ?>" size="32" maxlength="255">
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
		</table>
	</td>
</tr>
<?php
if ($editUser->isAdmin() /*&& $canManage*/) {
?>
<input type="hidden" name="customizeRights" id="customize_rights" value="false">
<tr id="user_type_dialog">
	<td style="padding-left: 4px; padding-top: 4px; padding-bottom: 4px;">
		<?php putGS("User Type"); ?>:
		<select name="UserType">
		<option value="">---</option>
<?php
$user_types = UserType::GetUserTypes();
$my_user_type = UserType::GetUserType($editUser->m_permissions);
foreach ($user_types as $index=>$user_type) {
	$user_type_name = htmlspecialchars($user_type->getName());
	echo "\t\t<option value=\"$user_type_name\"";
	if (gettype($my_user_type) == 'object' && $my_user_type->getName() == $user_type->getName())
		echo " selected";
	echo ">$user_type_name</option>\n";
}
?>
		</select>
	</td>
</tr>
<tr id="rights_show_link">
	<td style="padding-left: 6px; padding-top: 6px; padding-right: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('rights_dialog'); ToggleRowVisibility('user_type_dialog'); ToggleRowVisibility('rights_hide_link'); ToggleRowVisibility('rights_show_link'); ToggleBoolValue('customize_rights');">
			<img src="/admin/img/icon/viewmag+.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to customize user permissions"); ?>
		</a>
	</td>
</tr>
<tr id="rights_hide_link" style="display: none;">
	<td style="padding-left: 6px; padding-top: 6px; padding-right: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('rights_dialog'); ToggleRowVisibility('user_type_dialog'); ToggleRowVisibility('rights_hide_link'); ToggleRowVisibility('rights_show_link'); ToggleBoolValue('customize_rights');">
			<img src="/admin/img/icon/viewmag-.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to use existing user type permissions (discard customization)"); ?>
		</a>
	</td>
</tr>
<tr id="rights_dialog" style="display: none;">
	<td>
		<table border="0" cellspacing="0" cellpadding="6" align="center" width="100%">
			<tr>
				<td>
<?php require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/access_form.php"); ?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<?php
} // $editUser->isAdmin()
?>
<tr>
	<td>
		<table border="0" cellspacing="0" cellpadding="6" align="center" width="100%">
			<tr>
				<td colspan="2">
				<div align="center">
				<input type="submit" class="button" name="Save" value="<?php  putGS('Save changes'); ?>">
				<input type="button" class="button" name="Cancel" value="<?php putGS('Cancel'); ?>" onclick="location.href='<?php echo "/$ADMIN/users/?" . get_user_urlparams(); ?>'">
				</div>
				</td>
			</tr>
		</table>
	</td>
</tr>
</table>
</form>
