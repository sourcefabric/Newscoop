B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Articles})
<!sql query "SELECT Number, Name, IdLanguage FROM Articles WHERE 1=0" q_art>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({aaa}, {AddArticle})

B_STYLE
E_STYLE

B_PBODY1

<!sql setdefault lang 0>dnl
<!sql setdefault pub 0>dnl
<!sql setdefault iss 0>dnl
<!sql setdefault ssect 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Number, Name, IdLanguage FROM Articles WHERE IdPublication=?pub AND NrIssue=?iss AND NrSection=?ssect ORDER BY Number, IdLanguage" q_art>dnl
B_PBAR
    X_PBUTTON({X_ROOT/pub/issues/sections/articles/?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Section=<!sql print #ssect>&Language=<!sql print #lang>}, {Articles})
<!sql if $aaa>dnl
    X_PBUTTON({X_ROOT/pub/issues/sections/articles/add.xql?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Section=<!sql print #ssect>&Language=<!sql print #lang>}, {Add new article})
<!sql endif>dnl
X_PSEP2
<FORM NAME="FORM_ART" METHOD="GET">
<!sql if $NUM_ROWS>dnl
<SELECT NAME="art" ONCHANGE="var f = this.form.art; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/img.xql?lang=<!sql print #lang>&amp;pub=<!sql print #pub>&amp;iss=<!sql print #iss>&amp;ssect=<!sql print #ssect>&amp;' + v; if (v != 0) {{ parent.frames[1].location.href = x; }}">
	<OPTION VALUE="0">---Select article---<!sql print_loop q_art><OPTION VALUE="art=<!sql print ~q_art.Number>&slang=<!sql print ~q_art.IdLanguage>"><!sql print ~q_art.Name> (<!sql query "SELECT Name FROM Languages WHERE Id=?q_art.IdLanguage" q_ll><!sql print ~q_ll.Name><!sql free q_ll>)<!sql done>
</SELECT>
<!sql else>dnl
<SELECT DISABLED><OPTION>No articles</SELECT>
<!sql endif>dnl
</FORM>
E_PBAR

E_BODY

<!sql endif>dnl

E_DATABASE
E_HTML
