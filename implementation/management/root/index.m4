<HTML>
    <HEAD>
        X_EXPIRES
B_DATABASE{}dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Id, IdDefaultLanguage FROM Publications WHERE Site='$HTTP_HOST'" q_pub>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT IdPublication, Number, IdLanguage, FrontPage FROM Issues WHERE IdPublication=?q_pub.Id AND Published='Y' AND IdLanguage=?q_pub.IdDefaultLanguage ORDER BY Number DESC LIMIT 1" q_iss>dnl
<!sql if $NUM_ROWS == 0>dnl
<!sql query "SELECT IdPublication, Number, IdLanguage, FrontPage FROM Issues WHERE IdPublication=?q_pub.Id AND Published='Y' ORDER BY Number DESC LIMIT 1" q_iss>dnl
<!sql endif>dnl
<!sql if $NUM_ROWS>dnl
	X_REFRESH({0; URL=http://<!sql print $HTTP_HOST><!sql print $q_iss.FrontPage>?IdLanguage=<!sql print #q_iss.IdLanguage>&IdPublication=<!sql print #q_pub.Id>&NrIssue=<!sql print #q_iss.Number>})dnl
<!sql else>dnl
		Current issue not found.
<!sql endif>dnl
<!sql endif>
E_DATABASE{}dnl
    </HEAD>
</HTML>
