B_DATABASE
<!SQL SETDEFAULT IdPublication 0>dnl
<!SQL SET NUM_ROWS 0>dnl
<!SQL QUERY "SELECT * FROM Publications WHERE Id=?IdPublication" Publication>dnl
<!SQL IF $NUM_ROWS != 0>dnl

<HTML>
<HEAD>
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE>Welcome to <!SQL PRINT ~Publication.Name></TITLE>

<!SQL SETDEFAULT TOL_UserId 0>dnl
<!SQL SETDEFAULT TOL_UserKey 0>dnl
<!SQL SET NUM_ROWS 0>dnl
<!SQL QUERY "SELECT * FROM Users WHERE Id=?TOL_UserId AND KeyId=?TOL_UserKey" User>dnl
<!SQL IF $NUM_ROWS != 0>dnl

</HEAD>

<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<H1><!SQL PRINT ~Publication.Name></H1>

<FORM METHOD="POST" ACTION="change_password.xql">

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

<!SQL ELSE>dnl

	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=enter.xql">
</HEAD>

<!SQL ENDIF>dnl

</HTML>

</BODY>
</HTML>

<!SQL ELSE>dnl
	<P>No publication found.
<!SQL ENDIF>dnl
E_DATABASE
