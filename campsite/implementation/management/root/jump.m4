<HTML>
	<HEAD>
B_DATABASE{}dnl
<!sql setdefault HTTP_USER_AGENT "XXX">dnl
<!sql query "SELECT ('<!sql print $HTTP_USER_AGENT>' LIKE '%Mozilla%') OR ('<!sql print $HTTP_USER_AGENT>' LIKE '%MSIE%')" q_nav>dnl
<!sql query "SHOW FIELDS FROM Articles LIKE 'XXYYZZ'" q_fld>dnl

<!sql setdefault IdPublication 0>dnl
<!sql setdefault NrIssue 0>dnl
<!sql setdefault NrSection 0>dnl
<!sql setdefault Number 0>dnl
<!sql setdefault IdLanguage 0>dnl

<!sql set ok 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?IdPublication" q_pub>dnl
<!sql if $NUM_ROWS>dnl
	<!sql set NUM_ROWS 0>dnl
	<!sql query "SELECT * FROM Issues WHERE IdPublication=?IdPublication AND Number=?NrIssue AND IdLanguage=?IdLanguage" q_iss>dnl
	<!sql if $NUM_ROWS>dnl
		<!sql set ok 1>dnl
	<!sql else>dnl
		<!sql set NUM_ROWS 0>dnl
		<!sql query "SELECT * FROM Issues WHERE IdPublication=?IdPublication AND Number=?NrIssue AND IdLanguage=?q_pub.IdDefaultLanguage" q_iss>dnl
		<!sql if $NUM_ROWS>dnl
			<!sql set ok 1>dnl
		<!sql else>dnl
			<!sql set NUM_ROWS 0>dnl
			<!sql query "SELECT * FROM Issues WHERE IdPublication=?IdPublication AND Number=?NrIssue LIMIT 1" q_iss>dnl
			<!sql if $NUM_ROWS>dnl
				<!sql set ok 1>dnl
			<!sql else>dnl
	</HEAD>
<BODY>
	No such issue.
</BODY>
			<!sql endif>dnl
		<!sql endif>dnl
	<!sql endif>dnl
<!sql else>dnl
	</HEAD>
<BODY>
	No such publication.
</BODY>
<!sql endif>dnl

<!sql if $ok>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Articles WHERE IdPublication=?IdPublication AND NrIssue=?NrIssue AND NrSection=?NrSection AND  Number=?Number AND IdLanguage=?IdLanguage" q_art>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Languages WHERE Id=?IdLanguage" q_lang>dnl
<!sql if $NUM_ROWS>dnl
	<TITLE><!sql print ~q_art.Name></TITLE>
	<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=<!sql print ~q_lang.CodePage>">
	<META HTTP-EQUIV="Keywords" CONTENT="<!sql print ~q_art.Keywords>">
	</HEAD>
<BODY BGCOLOR=WHITE TEXT=WHITE LINK=WHITE ALINK=WHITE VLINK=WHITE onload="param='IdPublication=<!sql print #IdPublication>&NrIssue=<!sql print #NrIssue>&NrSection=<!sql print #NrSection>&NrArticle=<!sql print #Number>&IdLanguage=<!sql print #IdLanguage>';site='<!sql print $q_pub.Site>'; path='<!sql print $q_iss.SingleArticle>'; document.location.replace('http://' + site + path + '?' + param);">
<!sql if $q_nav.0 == 0>dnl
<!sql query "SHOW FIELDS FROM X?q_art.Type LIKE 'F%'" q_fld>dnl
<!sql print_loop q_fld>dnl
<!sql query "SELECT REPLACE(REPLACE(?q_fld.0, '<', '<!-- '), '>', ' -->') FROM X?q_art.Type WHERE NrArticle=?Number AND IdLanguage=?IdLanguage" q_fff>dnl
<p><!sql print $q_fff.0>
<!sql free q_fff>dnl
<!sql done>dnl
<!sql endif>dnl
</BODY>
<!sql else>dnl
	</HEAD>
<BODY>
	No such language.
</BODY>
<!sql endif>dnl
<!sql else>dnl
	</HEAD>
<BODY>
	No such article.
</BODY>
<!sql endif>dnl
<!sql endif>dnl

E_DATABASE{}dnl
</HTML>
