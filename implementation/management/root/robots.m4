<HTML>
B_DATABASE{}dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Id, IdDefaultLanguage, Name FROM Publications WHERE Site='$HTTP_HOST'" q_pub>dnl
<!sql if $NUM_ROWS>dnl
    <HEAD>
        <TITLE><!sql print ~q_pub.Name></TITLE>
    </HEAD>
<!sql query "SELECT * FROM Articles WHERE IdPublication=?q_pub.Id AND Published='Y' ORDER BY Number DESC LIMIT 200" q_art>dnl
<BODY>
<!sql print_loop q_art>dnl
<!sql query "SELECT COUNT(*) FROM Issues WHERE IdPublication=?q_pub.Id AND IdLanguage=?q_art.IdLanguage AND Published='Y'" q_iss>dnl
<!sql if @q_iss.0>dnl
<!sql print "\t<P><A HREF=\"/jump.xql\?IdPublication=#q_art.IdPublication&NrIssue=#q_art.NrIssue&NrSection=#q_art.NrSection&Number=#q_art.Number&IdLanguage=#q_art.IdLanguage\">~q_art.Name</A>\n">dnl
<!sql endif>dnl
<!sql done>dnl
</BODY>
<!sql endif>dnl
E_DATABASE{}dnl
</HTML>
