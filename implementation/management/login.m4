B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR*>)
<?php require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php'); ?>
B_HEAD
	X_TITLE(<*Login*>)
E_HEAD
B_BODY

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" >
<TR>
	<TD align="left" style="padding-top: 3px; padding-bottom: 3px; padding-left: 5px; border-bottom: 1px solid black;">
		<table>
		<tr>
			<td>
				<img src="/<?php echo $ADMIN; ?>/img/sign_big.gif" border="0">
			</td>
			<td style="font-size: 18pt; font-weight: bold; padding-left: 5px;">
				Campsite <?php  putGS("Login"); ?>
			</td>
		</tr>
		</table>				
	</TD>
</tr>
</TABLE>

<P>
B_DIALOG(<*Login*>, <*POST*>, <*do_login.php*>)
	X_DIALOG_TEXT(<*<?php  putGS('Please enter your user name and password'); ?>*>)
	B_DIALOG_INPUT(<*User name*>)
		<INPUT TYPE="TEXT" NAME="UserName" SIZE="32" MAXLENGTH="32" class="input_text">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Password*>)
		<INPUT TYPE="PASSWORD" NAME="UserPassword" SIZE="32" MAXLENGTH="32" class="input_text">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT name="selectlanguage" class="input_select">
		    <?php 
			foreach($languages as $key=>$larr){
			    //$lcode=key($larr[]);
			    $lval=$larr['name'];
			    print "<option value='$key'>$lval";
			}
		    ?>
		</select>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		SUBMIT(<*Login*>, <*Login*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>
<?php  if (file_exists("./guest_include.php")) require("./guest_include.php"); ?>
X_HR
X_COPYRIGHT
E_BODY
E_HTML