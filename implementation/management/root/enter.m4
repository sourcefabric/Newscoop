B_DATABASE
<!SQL SET NUM_ROWS 0>dnl
<!SQL QUERY "SELECT * FROM Publications WHERE Site='?HTTP_HOST'" Publication>dnl
<!SQL IF $NUM_ROWS != 0>dnl

<HTML>
<HEAD>
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE>Welcome to <!SQL PRINT ~Publication.Name></TITLE>
</HEAD>

<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<H1><!SQL PRINT ~Publication.Name></H1>


<!SQL SETDEFAULT TOL_UserId 0>dnl
<!SQL SETDEFAULT TOL_UserKey 0>dnl
<!SQL SET NUM_ROWS 0>dnl
<!SQL QUERY "SELECT * FROM Users WHERE Id=?TOL_UserId AND KeyId=?TOL_UserKey" User>dnl
<!SQL IF $NUM_ROWS != 0>dnl

	<P><A HREF="">Change your account information</A>
	<P><A HREF="password.xql?IdPublication=<!SQL PRINT #Publication.Id>">Change your password</A>

<!SQL SET NUM_ROWS 0>dnl
<!SQL QUERY "SELECT * FROM Subscriptions WHERE IdUser=?User.Id AND IdPublication=?Publication.Id" Subscription>dnl
<!SQL IF $NUM_ROWS != 0>dnl

<!SQL IF @Subscription.Active == "Y">dnl

<P><TABLE BORDER="0" CELLSPACING="2" CELLPADDING="2" WIDTH="100%">
<TR BGCOLOR="#D0D0FF">
	<TH WIDTH="50%">Subscribed sections</TH>
	<TH WIDTH="50%">Available sections</TH>
</TR>
<TR BGCOLOR="#FFFFD0">
	<TD VALIGN="TOP">

<!SQL SET NUM_ROWS 0>dnl
<!SQL QUERY "SELECT * FROM SubsSections WHERE IdSubscription=?Subscription.Id ORDER BY StartDate DESC" Section>dnl
<!SQL IF $NUM_ROWS>dnl

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="2" WIDTH="100%">
<TR BGCOLOR="#DODOFF">
	<TH>Section</TH>
	<TH>Start Date</TH>
	<TH>Days</TH>
	<TH>Paid</TH>
	<TH>Continue</TH>
</TR>

<!SQL PRINT_LOOP Section>dnl
<TR>
	<TD><!SQL PRINT ~Section.SectionNumber></TD>
	<TD><!SQL PRINT ~Section.StartDate></TD>
	<TD><!SQL PRINT ~Section.Days></TD>
	<TD><!SQL IF @Section.Paid == "Y">Yes<!SQL ELSE>No<!SQL ENDIF></TD>
	<TD><A HREF="">Continue</A></TD>
</TR>
<!SQL DONE>dnl

</TABLE>

<!SQL ELSE>dnl
	<P>No subscriptions.
<!SQL ENDIF>dnl
<!SQL FREE Sections>dnl

	</TD>
	<TD VALIGN="TOP">
	</TD>
</TR>
</TABLE>

<!SQL ELSE>dnl

	<P>Your subscription to this publication has been disabled.
	Please contact the site administrator for further informations.

<!SQL ENDIF>dnl

<!SQL ELSE>dnl

	<P>You are not currently subscribed to this publication.
	Click on the <B>subscribe</B> button to subscribe.

	<FORM METHOD="POST" ACTION="sub_pub.xql">
	<INPUT TYPE="HIDDEN" NAME="IdPublication" VALUE="<!SQL PRINT ~Publication.Id>">
	<INPUT TYPE="SUBMIT" VALUE="    Subscribe    ">
	</FORM>

<!SQL ENDIF>dnl

<!SQL ELSE>dnl

	<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="2" WIDTH="100%">
	<TR BGCOLOR="#D0D0FF">
		<TH WIDTH="50%">Sign in</TH>
		<TH WIDTH="50%">Sign up</TH>
	</TR>
	<TR BGCOLOR="#FFFFD0">
		<TD WIDTH="50%" ALIGN="CENTER">
			<P>Do you already have an account?<BR>
			If so, enter your user name and password to proceed.

			<FORM METHOD="POST" ACTION="login.xql">

			<P>User name:
			<INPUT TYPE="TEXT" NAME="UserName" SIZE="16" MAXLENGTH="32">

			<P>Password:
			<INPUT TYPE="PASSWORD" NAME="Password" SIZE="16" MAXLENGTH="32">

			<P><INPUT TYPE="SUBMIT" VALUE="    Login    ">
			</FORM>
		</TD>
		<TD WIDTH="50%" ALIGN="CENTER">
			<P>No account?<BR>
			Click here to get one.

			<H1><A HREF="signup.xql">Sign-Up Here!</A></H1>
		</TD>
	</TR>
	</TABLE>

<!SQL ENDIF>dnl

</BODY>
</HTML>

</BODY>
</HTML>

<!SQL ELSE>dnl
	<P>No publication found matching this site.
<!SQL ENDIF>dnl
E_DATABASE
