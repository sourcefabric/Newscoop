INCLUDE_PHP_LIB(<**>)
B_DATABASE<**>
<?php 
    TODEFNUM('IdPublication');
    query("SELECT * FROM Publications WHERE Id=$IdPublication", 'Publication');

    if($NUM_ROWS != 0) { 
	fetchRow($Publication);
    ?>dnl

<HTML>
<HEAD>
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE>Welcome to <?php  pgetHVar($Publication,'Name'); ?></TITLE>

<?php 
    todefnum('TOL_UserId');
    todefnum('TOL_UserKey');
    query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'User');
    if ($NUM_ROWS != 0) {
	fetchRow($User);
    ?>dnl

</HEAD>

<BODY>
<H1><?php  pgetHVar($Publication,'Name'); ?></H1>

<FORM METHOD="POST" ACTION="change_password.php">

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2">
<TR BGCOLOR="#D0DOFF">
	<TH COLSPAN="2">Change your password:</TH>
</TR>
<TR BGCOLOR="#FFFFD0">
	<TD ALIGN="RIGHT">Old password:</TD>
	<TD><INPUT TYPE="PASSWORD" NAME="OldPassword" SIZE="32" MAXLENGTH="32"></TD>
</TR>
<TR BGCOLOR="#FFFFD0">
	<TD ALIGN="RIGHT">New password:</TD>
	<TD><INPUT TYPE="PASSWORD" NAME="NewPassword1" SIZE="32" MAXLENGTH="32"></TD>
</TR>
<TR BGCOLOR="#FFFFD0">
	<TD ALIGN="RIGHT">Re-type new password:</TD>
	<TD><INPUT TYPE="PASSWORD" NAME="NewPassword2" SIZE="32" MAXLENGTH="32"></TD>
</TR>
<TR BGCOLOR="#FFFFD0">
	<TD COLSPAN="2" ALIGN="CENTER"><INPUT TYPE="SUBMIT" VALUE="    Change    "></TD>
</TR>
</TABLE>

</FORM>

</BODY>

<?php  } else { ?>dnl

	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=enter.php">
</HEAD>

<?php  } ?>dnl

</HTML>

</BODY>
</HTML>

<?php  } else { ?>dnl
	<P>No publication found.
<?php  } ?>dnl
E_DATABASE<**>
