B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Languages})
<!sql query "SELECT Id, Name FROM Languages WHERE 1=0" q_lang>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mla}, {ManageLanguages})

B_STYLE
E_STYLE

B_PBODY1

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Id, Name FROM Languages ORDER BY Name" q_lang>dnl
B_PBAR
    X_PBUTTON({X_ROOT/languages/}, {Languages})
<!sql if $mla>dnl
    X_PBUTTON({X_ROOT/languages/add.xql}, {Add new language})
<!sql endif>dnl
X_PSEP2
<FORM NAME="FORM_LANG" METHOD="GET">
<!sql if $NUM_ROWS>dnl
<SELECT NAME="lng" ONCHANGE="var f=this.form.lng; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/i1.xql?lang=' + v; if (v != 0) {{ parent.frames[1].location.href = x; }}">
	<OPTION VALUE="0">---Select language---<!sql print_loop q_lang><OPTION VALUE="<!sql print ~q_lang.Id>"><!sql print ~q_lang.Name><!sql done>
</SELECT>
<!sql else>dnl
<SELECT DISABLED><OPTION>No languages</SELECT>
<!sql endif>dnl
</FORM>
E_PBAR

E_BODY

<!sql endif>dnl

E_DATABASE
E_HTML
