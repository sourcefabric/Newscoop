<?php
require_once($GLOBALS['g_campsiteDir']. '/classes/UserType.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/Country.php');

if (!isset($editUser) || gettype($editUser) != 'object') {
	camp_html_display_error(getGS('No such user account.'), "/$ADMIN/users/?".get_user_urlparams());
	exit;
}
$isNewUser = $editUser->getUserName() == '';
compute_user_rights($g_user, $canManage, $canDelete);
if (!$canManage && $editUser->getUserId() != $g_user->getUserId()) {
	if ($isNewUser) {
		$error = getGS("You do not have the right to create user accounts.");
	} else {
		$error = getGS('You do not have the right to change user account information.');
	}
	camp_html_display_error($error);
	exit;
}

$fields = array('UName', 'Name', 'Title', 'Gender', 'Age', 'EMail', 'City', 'StrAddress',
	'State', 'CountryCode', 'Phone', 'Fax', 'Contact', 'Phone2', 'PostalCode', 'Employer',
	'EmployerType', 'Position');
if ($isNewUser) {
	$action = 'do_add.php';
	foreach ($fields as $index=>$field) {
		$$field = Input::Get($field, 'string', '');
	}
} else {
	$action = 'do_edit.php';
	foreach ($fields as $index=>$field) {
		$$field = $editUser->getProperty($field);
	}
}
$userTypes = UserType::GetUserTypes();
$countries = Country::GetCountries(1);
$my_user_type = $editUser->getUserType();

?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>

<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/pwd_meter/js/pwd_meter_min.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/pwd_meter/js/pwd_generator_min.js"></script>
<link href="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/pwd_meter/css/default.css" rel="stylesheet" type="text/css" />


<form name="user_add" method="POST" action="<?php echo $action; ?>" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php echo SecurityToken::FormParameter(); ?>
<input type="hidden" name="uType" value="<?php echo $uType; ?>">
<?php
if (!$isNewUser) {
?>
<input type="hidden" name="User" value="<?php echo $editUser->getUserId(); ?>">
<?php
}
?>
<table border="0" cellspacing="0" cellpadding="0" class="box_table">
<tr>
	<td align="left">
		<table border="0" cellspacing="0" cellpadding="3" align="left">
			<tr>
				<td align="right" nowrap><?php putGS("Account name"); ?>:</td>
<?php
if (!$isNewUser) {
?>
				<td align="left" nowrap><b><?php p(htmlspecialchars($editUser->getUserName())); ?></b></td>
<?php
} else {
?>
				<td><input type="text" class="input_text" name="UName" size="32" maxlength="32" value="<?php p(htmlspecialchars($UName)); ?>" alt="blank" emsg="<?php putGS("You must fill in the $1 field.", "Account name"); ?>"></td>
			</tr>

                <tr>
		  <td nowrap><?php putGS("Password Generator"); ?>:</td>
                  <td>
                    <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <input type="button" class="button" value="<?php putGS('Generate'); ?>" onClick="GeneratePassword()">
                      </td>
                      <td style="padding-left:6px;">
                        <div id="passtext"> </div>
                      </td>
                      <td style="padding-left:6px;">
                        <input type="button" class="button" value="<?php putGS('Clean'); ?>" onClick="cleanGeneratedPasswords()">
                      </td>
                    </tr>
                    </table>
                  </td>
                </tr>

			<tr>
				<td align="right"><?php putGS("Password"); ?>:</td>
				<td>
                                  <table cellpadding="0" cellspacing="0">
                                  <tr>
                                    <td>
                                      <input type="password" class="input_text" id="password" name="password" size="16" maxlength="32" alt="length|6" emsg="<?php putGS("The password must be at least 6 characters long and both passwords should match."); ?>" onkeyup="chkPass(this.value);">
                                    </td>
                                    <td style="padding-left:6px;">
                                      <div id="score">0%</div>
                                      <div id="scorebar">&nbsp;</div>
                                    </td>
                                    <td style="padding-left:4px;">
                                      <div id="complexity">Too Short</div>
                                    </td>
                                  </tr>
                                  </table>

                                  <div id="div_nLength" style="display:none"></div>
                    <div id="nLength" style="display:none"></div>
                    <div id="nLengthBonus" style="display:none;"></div>
                    <div id="div_nAlphaUC" style="display:none;"></div>
                    <div id="nAlphaUC" style="display:none;"></div>
                    <div id="nAlphaUCBonus" style="display:none;"></div>
                    <div id="div_nAlphaLC" style="display:none;"></div>
                    <div id="nAlphaLC" style="display:none;"></div>
                    <div id="nAlphaLCBonus" style="display:none;"></div>
                    <div id="div_nNumber" style="display:none;"></div>
                    <div id="nNumber" style="display:none;"></div>
                    <div id="nNumberBonus" style="display:none;"></div>
                    <div id="div_nSymbol" style="display:none;"></div>
                    <div id="nSymbol" style="display:none;"></div>
                    <div id="nSymbolBonus" style="display:none;"></div>
                    <div id="div_nMidChar" style="display:none;"></div>
                    <div id="nMidChar" style="display:none;"></div>
                    <div id="nMidCharBonus" style="display:none;"></div>
                    <div id="div_nRequirements" style="display:none;"></div>
                    <div id="nRequirements" style="display:none;"></div>
                    <div id="nRequirementsBonus" style="display:none;"></div>
                    <div id="div_nAlphasOnly" style="display:none;"></div>
                    <div id="nAlphasOnly" style="display:none;"></div>
                    <div id="nAlphasOnlyBonus" style="display:none;"></div>
                    <div id="div_nNumbersOnly" style="display:none;"></div>
                    <div id="nNumbersOnly" style="display:none;"></div>
                    <div id="nNumbersOnlyBonus" style="display:none;"></div>
                    <div id="div_nRepChar" style="display:none;"></div>
                    <div id="nRepChar" style="display:none;"></div>
                    <div id="nRepCharBonus" style="display:none;"></div>
                    <div id="div_nConsecAlphaUC" style="display:none;"></div>
                    <div id="nConsecAlphaUC" style="display:none;"></div>
                    <div id="nConsecAlphaUCBonus" style="display:none;"></div>
                    <div id="div_nConsecAlphaLC" style="display:none;"></div>
                    <div id="nConsecAlphaLC" style="display:none;"></div>
                    <div id="nConsecAlphaLCBonus" style="display:none;"></div>
                    <div id="div_nConsecNumber" style="display:none;"></div>
                    <div id="nConsecNumber" style="display:none;"></div>
                    <div id="nConsecNumberBonus" style="display:none;"></div>
                    <div id="div_nSeqAlpha" style="display:none;"></div>
                    <div id="nSeqAlpha" style="display:none;"></div>
                    <div id="nSeqAlphaBonus" style="display:none;"></div>
                    <div id="div_nSeqNumber" style="display:none;"></div>
                    <div id="nSeqNumber" style="display:none;"></div>
                    <div id="nSeqNumberBonus" style="display:none;"></div>
				</td>
			</tr>
			<tr>
				<td align="right"><?php putGS("Confirm password"); ?>:</td>
				<td>
				<input type="password" class="input_text" name="passwordConf" size="16" maxlength="32" alt="length|6" emsg="<?php putGS("The confirm password must be at least 6 characters long and both passwords should match."); ?>">
                                <input type="text" id="passwordTxt" name="passwordTxt" class="hide" />
				</td>
<?php
}
?>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Full Name"); ?>:</td>
				<td><input type="text" class="input_text" name="Name" VALUE="<?php p(htmlspecialchars($Name)); ?>" size="32" maxlength="128" alt="blank" emsg="<?php putGS("You must fill in the $1 field.", "Full Name");?>">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("E-Mail"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="EMail" value="<?php p(htmlspecialchars($EMail)); ?>" size="32">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Phone"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Phone" value="<?php p(htmlspecialchars($Phone)); ?>" size="20">
				</td>
			</tr>

			<?php
			if ($isNewUser && ($uType == "Staff")) {
			?>
			<tr>
				<td align="right"><?php putGS("Type"); ?>:</td>
				<td>
				<select name="Type" class="input_select" alt="select" emsg="<?php putGS("You must select a $1", "Type"); ?>">
				<option value=""><?php putGS("Make a selection"); ?></option>
				<?php
				$Type = Input::Get('Type', 'int', 0);
				foreach ($userTypes as $tmpUserType) {
					camp_html_select_option($tmpUserType->getId(), $Type, $tmpUserType->getName());
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
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmagplus.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to change password"); ?>
		</a>
	</td>
</tr>
<tr id="password_hide_link" style="display: none;">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('password_dialog'); ToggleRowVisibility('password_hide_link'); ToggleRowVisibility('password_show_link'); ToggleBoolValue('set_password');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmagminus.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to leave password unchanged"); ?>
		</a>
	</td>
</tr>
<tr id="password_dialog" style="display: none;">
	<td>
		<table border="0" cellspacing="0" cellpadding="3" align="center" width="100%">
		<?php
		if ( ($userId == $g_user->getUserId()) && !$isNewUser) {
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
		  <td nowrap><?php putGS("Password Generator"); ?>:</td>
                  <td>
                    <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
                        <input type="button" class="button" value="<?php putGS('Generate'); ?>" onClick="GeneratePassword()">
                      </td>
                      <td style="padding-left:6px;">
                        <div id="passtext"> </div>
                      </td>
                      <td style="padding-left:6px;">
                        <input type="button" class="button" value="<?php putGS('Clean'); ?>" onClick="cleanGeneratedPasswords()">
                      </td>
                    </tr>
                    </table>
                  </td>
                </tr>

		<tr>
                  <td align="right" nowrap width="1%"><?php putGS("Password"); ?>:</td>
		  <td>
                    <table cellpadding="0" cellspacing="0">
                    <tr>
                      <td>
			<input type="password" class="input_text" id="password" name="password" size="16" maxlength="32" onkeyup="chkPass(this.value);">
                      </td>
                      <td style="padding-left:6px;">
                        <div id="score">0%</div>
                        <div id="scorebar">&nbsp;</div>
                      </td>
                      <td style="padding-left:4px;">
                        <div id="complexity">Too Short</div>
                      </td>
                    </tr>
                    </table>

                    <div id="div_nLength" style="display:none"></div>
                    <div id="nLength" style="display:none"></div>
                    <div id="nLengthBonus" style="display:none;"></div>
                    <div id="div_nAlphaUC" style="display:none;"></div>
                    <div id="nAlphaUC" style="display:none;"></div>
                    <div id="nAlphaUCBonus" style="display:none;"></div>
                    <div id="div_nAlphaLC" style="display:none;"></div>
                    <div id="nAlphaLC" style="display:none;"></div>
                    <div id="nAlphaLCBonus" style="display:none;"></div>
                    <div id="div_nNumber" style="display:none;"></div>
                    <div id="nNumber" style="display:none;"></div>
                    <div id="nNumberBonus" style="display:none;"></div>
                    <div id="div_nSymbol" style="display:none;"></div>
                    <div id="nSymbol" style="display:none;"></div>
                    <div id="nSymbolBonus" style="display:none;"></div>
                    <div id="div_nMidChar" style="display:none;"></div>
                    <div id="nMidChar" style="display:none;"></div>
                    <div id="nMidCharBonus" style="display:none;"></div>
                    <div id="div_nRequirements" style="display:none;"></div>
                    <div id="nRequirements" style="display:none;"></div>
                    <div id="nRequirementsBonus" style="display:none;"></div>
                    <div id="div_nAlphasOnly" style="display:none;"></div>
                    <div id="nAlphasOnly" style="display:none;"></div>
                    <div id="nAlphasOnlyBonus" style="display:none;"></div>
                    <div id="div_nNumbersOnly" style="display:none;"></div>
                    <div id="nNumbersOnly" style="display:none;"></div>
                    <div id="nNumbersOnlyBonus" style="display:none;"></div>
                    <div id="div_nRepChar" style="display:none;"></div>
                    <div id="nRepChar" style="display:none;"></div>
                    <div id="nRepCharBonus" style="display:none;"></div>
                    <div id="div_nConsecAlphaUC" style="display:none;"></div>
                    <div id="nConsecAlphaUC" style="display:none;"></div>
                    <div id="nConsecAlphaUCBonus" style="display:none;"></div>
                    <div id="div_nConsecAlphaLC" style="display:none;"></div>
                    <div id="nConsecAlphaLC" style="display:none;"></div>
                    <div id="nConsecAlphaLCBonus" style="display:none;"></div>
                    <div id="div_nConsecNumber" style="display:none;"></div>
                    <div id="nConsecNumber" style="display:none;"></div>
                    <div id="nConsecNumberBonus" style="display:none;"></div>
                    <div id="div_nSeqAlpha" style="display:none;"></div>
                    <div id="nSeqAlpha" style="display:none;"></div>
                    <div id="nSeqAlphaBonus" style="display:none;"></div>
                    <div id="div_nSeqNumber" style="display:none;"></div>
                    <div id="nSeqNumber" style="display:none;"></div>
                    <div id="nSeqNumberBonus" style="display:none;"></div>
			</td>
		</tr>

		<tr>
			<td align="right" nowrap width="1%"><?php putGS("Confirm password"); ?>:</td>
			<td>
			<input type="password" class="input_text" id="passwordConf" name="passwordConf" size="16" maxlength="32">
                        <input type="text" id="passwordTxt" name="passwordTxt" class="hide" />
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
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmagplus.png" id="my_icon" border="0" align="center">
			<?php putGS("Show more user details"); ?>
		</a>
	</td>
</tr>
<tr id="user_details_hide_link" style="display: none;">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('user_details_dialog'); ToggleRowVisibility('user_details_hide_link'); ToggleRowVisibility('user_details_show_link');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmagminus.png" id="my_icon" border="0" align="center">
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
				<SELECT class="input_select" name="Title">
				<?php
				camp_html_select_option(getGS("Mr."), $Title, getGS("Mr."));
				camp_html_select_option(getGS("Mrs."), $Title, getGS("Mrs."));
				camp_html_select_option(getGS("Ms."), $Title, getGS("Ms."));
				camp_html_select_option(getGS("Dr."), $Title, getGS("Dr."));
				?>
				</SELECT>
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
				<SELECT name="Age" class="input_select">
				<?php
				camp_html_select_option("0-17", $Age, getGS("under 18"));
				camp_html_select_option("18-24", $Age, "18-24");
				camp_html_select_option("25-39", $Age, "25-39");
				camp_html_select_option("40-49", $Age, "40-49");
				camp_html_select_option("50-65", $Age, "50-65");
				camp_html_select_option("65-", $Age, getGS("65 or over"));
				?>
				</SELECT>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("City"); ?>:</td>
				<td>
				<input type="text" class="input_text" NAME="City" VALUE="<?php p(htmlspecialchars($City)); ?>" size="32">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Street Address"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="StrAddress" value="<?php  p(htmlspecialchars($StrAddress)); ?>" size="32" maxlength="255">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Postal Code"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="PostalCode" value="<?php p(htmlspecialchars($PostalCode)); ?>" size="10">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("State"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="State" value="<?php p(htmlspecialchars($State)); ?>" size="32">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Country"); ?>:</td>
				<td>
				<SELECT name="CountryCode" class="input_select">
				<?php
				foreach ($countries as $country) {
					camp_html_select_option($country->getCode(), $CountryCode, $country->getName());
				}
				?>
				</SELECT>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Fax"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Fax" value="<?php p(htmlspecialchars($Fax)); ?>" size="20">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Contact Person"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Contact" value="<?php  p(htmlspecialchars($Contact)); ?>" size="32">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Second Phone"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Phone2" value="<?php  p(htmlspecialchars($Phone2)); ?>" size="20">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Employer"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Employer" value="<?php  p(htmlspecialchars($Employer)); ?>" size="30">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Employer Type"); ?>:</td>
				<td>
					<SELECT name="EmployerType" class="input_select">
					<?php
					if ($EmployerType == "") {
						$EmployerType = "Other";
					}
					camp_html_select_option('Corporate', $EmployerType, getGS('Corporate'));
					camp_html_select_option('NGO', $EmployerType, getGS('Non-Governmental Organisation'));
					camp_html_select_option('Government Agency', $EmployerType, getGS('Government Agency'));
					camp_html_select_option('Academic', $EmployerType, getGS('Academic'));
					camp_html_select_option('Media', $EmployerType, getGS('Media'));
					camp_html_select_option('Other', $EmployerType, getGS('Other'));
					?>
					</SELECT>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Position"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Position" value="<?php p(htmlspecialchars($Position)); ?>" size="30">
				</td>
			</tr>
		</table>
	</td>
</tr>
<?php
if ($editUser->isAdmin() && $canManage) {
?>
<input type="hidden" name="customizeRights" id="customize_rights" value="false">
<tr id="user_type_dialog">
	<td style="padding-left: 4px; padding-top: 4px; padding-bottom: 4px;">
		<?php putGS("User Type"); ?>:
		<select name="UserType">
		<option value="">---</option>
		<?php
		foreach ($userTypes as $user_type) {
			camp_html_select_option($user_type->getId(), $my_user_type, $user_type->getName());
		}
		?>
		</select>
	</td>
</tr>
<tr id="rights_show_link">
	<td style="padding-left: 6px; padding-top: 6px; padding-right: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('rights_dialog'); ToggleRowVisibility('user_type_dialog'); ToggleRowVisibility('rights_hide_link'); ToggleRowVisibility('rights_show_link'); ToggleBoolValue('customize_rights');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmagplus.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to customize user permissions"); ?>
		</a>
	</td>
</tr>
<tr id="rights_hide_link" style="display: none;">
	<td style="padding-left: 6px; padding-top: 6px; padding-right: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('rights_dialog'); ToggleRowVisibility('user_type_dialog'); ToggleRowVisibility('rights_hide_link'); ToggleRowVisibility('rights_show_link'); ToggleBoolValue('customize_rights');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmagminus.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to use existing user type permissions (discard customization)"); ?>
		</a>
	</td>
</tr>
<tr id="rights_dialog" style="display: none;">
	<td>
		<table border="0" cellspacing="0" cellpadding="6" align="center" width="100%">
			<tr>
				<td>
<?php require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/users/access_form.php"); ?>
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
			<td colspan="2" align="center">
				<input type="submit" class="button" name="Save" value="<?php  putGS('Save'); ?>">
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</form>
<?php if ($isNewUser) { ?>
<script>
document.user_add.UName.focus();
</script>
<?php } ?>