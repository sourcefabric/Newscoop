INCLUDE_PHP_LIB(<*./priv*>)
B_DATABASE<**>
<?
    query("SELECT * FROM Publications WHERE Site='$HTTP_HOST'", 'Publication');
    if ($NUM_ROWS != 0) { 
	fetchRow($Publication);
    ?>dnl

<HTML>
<HEAD>
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE>Welcome to <? pgetHVar($Publication,'Name'); ?></TITLE>
</HEAD>

<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<H1><? pgetHVar($Publication,'Name'); ?></H1>


<?
    todefnum('TOL_UserId');
    todefnum('TOL_UserKey');
    query("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'User');
    if ($NUM_ROWS != 0) { 
	fetchRow($User);
    ?>dnl

	<P><A HREF="">Change your account information</A>
	<P><A HREF="password.php?IdPublication=<? pgetUVar($Publication,'Id'); ?>">Change your password</A>

    <? 
	query("SELECT * FROM Subscriptions WHERE IdUser=".getSVar($User,'Id')." AND IdPublication=".getSVar($Publication,'Id'), 'Subscription');
	if($NUM_ROWS != 0) { 
	    fetchRow($Subscription);
	?>dnl

<?
    if (getVar($Subscription,'Active') == "Y") { ?>dnl

<P><TABLE BORDER="0" CELLSPACING="2" CELLPADDING="2" WIDTH="100%">
<TR BGCOLOR="#D0D0FF">
	<TH WIDTH="50%">Subscribed sections</TH>
	<TH WIDTH="50%">Available sections</TH>
</TR>
<TR BGCOLOR="#FFFFD0">
	<TD VALIGN="TOP">

<?
    query( "SELECT * FROM SubsSections WHERE IdSubscription=".getSVar($Subscription,'Id')." ORDER BY StartDate DESC", 'Section');
    if ($NUM_ROWS) { ?>dnl

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="2" WIDTH="100%">
<TR BGCOLOR="#DODOFF">
	<TH>Section</TH>
	<TH>Start Date</TH>
	<TH>Days</TH>
	<TH>Paid</TH>
	<TH>Continue</TH>
</TR>

<?
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($Section);
	?>dnl
<TR>
	<TD><? pgetHVar($Section,'SectionNumber'); ?></TD>
	<TD><? pgetHVar($Section,'StartDate'); ?></TD>
	<TD><? pgetHVar($Section,'Days'); ?></TD>
	<TD><? if (getVar($Section,'Paid') == "Y") { ?>Yes<? } else { ?>No<? } ?></TD>
	<TD><A HREF="">Continue</A></TD>
</TR>
<? } //loop
?>dnl

</TABLE>

<? } else { ?>dnl
	<P>No subscriptions.
<? } ?>dnl
	</TD>
	<TD VALIGN="TOP">
	</TD>
</TR>
</TABLE>

<? } else { ?>dnl

	<P>Your subscription to this publication has been disabled.
	Please contact the site administrator for further informations.

<? } ?>dnl

<? } else { ?>dnl

	<P>You are not currently subscribed to this publication.
	Click on the <B>subscribe</B> button to subscribe.

	<FORM METHOD="POST" ACTION="sub_pub.php">
	<INPUT TYPE="HIDDEN" NAME="IdPublication" VALUE="<? pgetHVar($Publication,'Id'); ?>">
	<INPUT TYPE="SUBMIT" VALUE="    Subscribe    ">
	</FORM>

<? } ?>dnl

<? } else { ?>dnl

	<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="2" WIDTH="100%">
	<TR BGCOLOR="#D0D0FF">
		<TH WIDTH="50%">Sign in</TH>
		<TH WIDTH="50%">Sign up</TH>
	</TR>
	<TR BGCOLOR="#FFFFD0">
		<TD WIDTH="50%" ALIGN="CENTER">
			<P>Do you already have an account?<BR>
			If so, enter your user name and password to proceed.

			<FORM METHOD="POST" ACTION="login.php">

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

			<H1><A HREF="signup.php">Sign-Up Here!</A></H1>
		</TD>
	</TR>
	</TABLE>

<? } ?>dnl

</BODY>
</HTML>

</BODY>
</HTML>

<? } else { ?>dnl
	<P>No publication found matching this site.
<? } ?>dnl
E_DATABASE<**>
