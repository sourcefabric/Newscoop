B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({ManageSubscriptions})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding Subscription})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add subscriptions.})
<!sql endif>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault User 0>dnl
<!sql setdefault Pub 0>dnl
<!sql setdefault Subs 0>dnl
<!sql setdefault cStartDate 0>dnl
<!sql setdefault cSection 0>dnl
<!sql setdefault cDays 0>dnl
<!sql set Success 1>dnl
B_HEADER({Adding Sections})
B_HEADER_BUTTONS
X_HBUTTON({Sections}, {users/subscriptions/sections/?User=<!sql print #User>&Pub=<!sql print #Pub>&Subs=<!sql print #Subs>})
X_HBUTTON({Subscriptions}, {users/subscriptions/?User=<!sql print #User>})
X_HBUTTON({Users}, {users/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT UName FROM Users WHERE Id=?User" q_usr>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

B_CURRENT
X_CURRENT({User account:}, {<B><!sql print ~q_usr.UName></B>})
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<P>
B_MSGBOX({Adding sections to subscription})

<!sql if ?cSection != 0>

<!sql query "INSERT IGNORE INTO SubsSections SET IdSubscription=?Subs, SectionNumber='?cSection', StartDate='?cStartDate', Days='?cDays'">dnl
<!sql if $AFFECTED_ROWS>dnl
	X_MSGBOX_TEXT({<LI>The section was added successfully.</LI>})
<!sql else>dnl
	X_MSGBOX_TEXT({<LI>The section could not be added.</LI><LI>Please check if there isn't another subscription with the same section.</LI>})
<!sql endif>dnl
	B_MSGBOX_BUTTONS
<!sql if $AFFECTED_ROWS>dnl
		<A HREF="X_ROOT/users/subscriptions/sections/?Pub=<!sql print #Pub>&User=<!sql print #User>&Subs=<!sql print #Subs>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/users/subscriptions/sections/add.xql?Pub=<!sql print #Pub>&User=<!sql print #User>&Subs=<!sql print #Subs>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS

<!sql else>

<!sql query "SELECT DISTINCT Number FROM Sections where IdPublication=?Pub" q_sect>
<!sql print_loop q_sect>dnl
<!sql query "INSERT IGNORE INTO SubsSections SET IdSubscription=?Subs, SectionNumber='?q_sect.0', StartDate='?cStartDate', Days='?cDays'"><br>dnl
	<!sql if $AFFECTED_ROWS = 0>dnl
	<!sql set Success 0>
	<!sql endif>
<!sql done>dnl

<!sql if $Success>dnl
	X_MSGBOX_TEXT({<LI>The sections were added successfully.</LI>})
<!sql else>dnl
	X_MSGBOX_TEXT({<LI>The sections could not be added successfully.
Some of them were already added !</LI>})
<!sql endif>dnl
	B_MSGBOX_BUTTONS
<!sql if $Success>dnl
		<A HREF="X_ROOT/users/subscriptions/sections/?Pub=<!sql print #Pub>&User=<!sql print #User>&Subs=<!sql print #Subs>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
<!sql else>dnl
		<A HREF="X_ROOT/users/subscriptions/sections/add.xql?Pub=<!sql print #Pub>&User=<!sql print #User>&Subs=<!sql print #Subs>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
<!sql endif>dnl
	E_MSGBOX_BUTTONS
<!sql endif>

E_MSGBOX
<P>

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such user account.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
