B_HTML
INCLUDE_PHP_LIB(<*.*>)
B_HEAD
	X_TITLE(<*Login*>)
E_HEAD

B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Login*>)
X_HEADER_NO_BUTTONS
E_HEADER

<P>
B_DIALOG(<*Login*>, <*POST*>, <*do_login.php*>)
	X_DIALOG_TEXT(<*<? putGS('Please enter your user name and password'); ?>*>)
	B_DIALOG_INPUT(<*User name*>)
		<INPUT TYPE="TEXT" NAME="UserName" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Password*>)
		<INPUT TYPE="PASSWORD" NAME="UserPassword" SIZE="32" MAXLENGTH="32">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Language*>)
		<SELECT name=selectlanguage>
		    <?
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
<? if (file_exists("./guest_include.php")) require("./guest_include.php"); ?>
X_HR
X_COPYRIGHT
E_BODY
E_HTML

