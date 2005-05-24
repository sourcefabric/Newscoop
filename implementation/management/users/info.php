<?php

check_basic_access($_REQUEST);
if (!isset($editUser) || gettype($editUser) != 'object') {
	CampsiteInterface::DisplayError('No such user account.');
	exit;
}

if ($editUser->getUserName() == '')
	$action = 'do_add.php';
else
	$action = 'do_info.php';

?>
<P><form name="dialog" method="POST" action="<?php echo $action; ?>">
<input type="hidden" name="uType" value="<?php echo $uType; ?>">
<?php if ($editUser->getUserName() != '') { ?>
<input type="hidden" name="User" value="<?php echo $editUser->getId(); ?>">
<?php } ?>
<table border="0" cellspacing="0" cellpadding="6" class="table_input" align="center">
	<tr>
		<td align="right" nowrap><?php putGS("User name"); ?>:</td>
		<td align="left" nowrap><b><?php pencHTML($editUser->getUserName()); ?></b></td>
	</tr>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Full Name"); ?>:</TD>
		<TD><INPUT TYPE="TEXT" class="input_text" NAME="Name" VALUE="<?php pencHTML($editUser->getName()); ?>" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Title"); ?>:</TD>
		<TD>
<?php
	CampsiteInterface::CreateSelect("Title", array("Mr.", "Mrs.", "Ms.", "Dr."),
		$editUser->getProperty('Title'), 'class="input_select"');
?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Gender"); ?>:</TD>
		<TD>
		<?php $gender = $editUser->getProperty('Gender'); ?>
		<INPUT TYPE=RADIO NAME=Gender VALUE="M"<?php if($gender == "M") { ?> CHECKED<?php  } ?>><?php  putGS('Male'); ?>
		<INPUT TYPE=RADIO NAME=Gender VALUE="F"<?php if($gender== "F") { ?> CHECKED<?php  } ?>><?php  putGS('Female'); ?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Age"); ?>:</TD>
		<TD>
<?php
	CampsiteInterface::CreateSelect("Age", array("0-17"=>getGS("under 18"),
			"18-24"=>"18-24", "25-39"=>"25-39", "40-49"=>"40-49", "50-65"=>"50-65",
			"65-"=>getGS('65 or over')),
		$editUser->getProperty('Age'), 'class="input_select"', true);
?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("E-Mail"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="EMail" VALUE="<?php pencHTML($editUser->getProperty('EMail')); ?>" SIZE="32" MAXLENGTH="128">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("City"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="City" VALUE="<?php  pencHTML($editUser->getProperty('City')); ?>" SIZE="32" MAXLENGTH="60">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Street Address"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="StrAddress" VALUE="<?php  pencHTML($editUser->getProperty('StrAddress')); ?>" SIZE="40" MAXLENGTH="255">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Postal Code"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="PostalCode" VALUE="<?php  pencHTML($editUser->getProperty('PostalCode')); ?>" SIZE="10" MAXLENGTH="10">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("State"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="State" VALUE="<?php  pencHTML($editUser->getProperty('State')); ?>" SIZE="32" MAXLENGTH="32">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Country"); ?>:</TD>
		<TD>
<?php
	$countries_list[""] = "";
	query ("SELECT Code, Name FROM Countries where IdLanguage = 1", 'countries');
	for($loop = 0; $loop < $NUM_ROWS; $loop++) {
		fetchRow($countries);
		$countries_list[getHVar($countries,'Code')] = getHVar($countries,'Name');
	}
	CampsiteInterface::CreateSelect("CountryCode", $countries_list,
		$editUser->getProperty('CountryCode'), 'class="input_select"', true);
?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Phone"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="Phone" VALUE="<?php  pencHTML($editUser->getProperty('Phone')); ?>" SIZE="20" MAXLENGTH="20">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Fax"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="Fax" VALUE="<?php  pencHTML($editUser->getProperty('Fax')); ?>" SIZE="20" MAXLENGTH="20">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Contact Person"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="Contact" VALUE="<?php  pencHTML($editUser->getProperty('Contact')); ?>" SIZE="32" MAXLENGTH="64">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Second Phone"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="Phone2" VALUE="<?php  pencHTML($editUser->getProperty('Phone2')); ?>" SIZE="20" MAXLENGTH="20">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Employer"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="Employer" VALUE="<?php  pencHTML($editUser->getProperty('Employer')); ?>" SIZE="30" MAXLENGTH="30">
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Employer Type"); ?>:</TD>
		<TD>
<?php
	$employerTypes[''] = '';
	$employerTypes['Corporate'] = getGS('Corporate');
	$employerTypes['NGO'] = getGS('Non-Governmental Organisation');
	$employerTypes['Government Agency'] = getGS('Government Agency');
	$employerTypes['Academic'] = getGS('Academic');
	$employerTypes['Media'] = getGS('Media');
	CampsiteInterface::CreateSelect("EmployerType", $employerTypes,
		$editUser->getProperty('EmployerType'), 'class="input_select"', true);
?>
		</TD>
	</TR>
	<TR>
		<TD ALIGN="RIGHT" nowrap><?php  putGS("Position"); ?>:</TD>
		<TD>
		<INPUT TYPE="TEXT" class="input_text" NAME="Position" VALUE="<?php  pencHTML($editUser->getProperty('Position')); ?>" SIZE="30" MAXLENGTH="30">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save changes'); ?>">
		<INPUT TYPE="button" class="button" NAME="Cancel" VALUE="<?php putGS('Cancel'); ?>" ONCLICK="location.href='<?php echo "/$ADMIN/users/?" . get_user_urlparams(); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
</FORM>
