B_DATABASE
<!SQL SETDEFAULT IdPublication 0>dnl
<!SQL SET NUM_ROWS 0>dnl
<!SQL QUERY "SELECT * FROM Publications WHERE Id=?IdPublication" Publication>dnl
<!SQL IF $NUM_ROWS != 0>dnl

<!SQL SETDEFAULT TOL_UserId 0>dnl
<!SQL SETDEFAULT TOL_UserKey 0>dnl
<!SQL SET NUM_ROWS 0>dnl
<!SQL QUERY "SELECT * FROM Users WHERE Id=?TOL_UserId AND KeyId=?TOL_UserKey" User>dnl
<!SQL IF $NUM_ROWS != 0>dnl


<HTML>
<HEAD>
	<META HTTP-EQUIV="Expires" CONTENT="now">
	<TITLE>Welcome to <!SQL PRINT ~Publication.Name></TITLE>

<!SQL SET AFFECTED_ROWS 0>dnl
<!SQL QUERY "INSERT IGNORE INTO Subscriptions SET IdUser=?User.Id, IdPublication=?Publication.Id, Active='Y'">dnl
<!SQL IF $AFFECTED_ROWS != 0>dnl
	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=sections.xql?IdPublication=<!SQL PRINT #Publication.Id>">
<!SQL ENDIF>dnl

</HEAD>

<!SQL IF $AFFECTED_ROWS == 0>dnl
<BODY BGCOLOR="WHITE" TEXT="BLACK" LINK="DARKBLUE" ALINK="RED" VLINK="DARKBLUE">
<H1><!SQL PRINT ~Publication.Name></H1>

<BLOCKQUOTE>
	<P>You could not be subscribed to this publication.
	Try again and if the problem persists, contact the site administrator.
</BLOCKQUOTE>

</BODY>
<!SQL ENDIF>dnl

</HTML>

<!SQL ELSE>dnl
	<P>No publication found matching this site.
<!SQL ENDIF>dnl
E_DATABASE
