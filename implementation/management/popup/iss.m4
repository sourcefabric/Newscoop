B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Issues})
<!sql query "SELECT Number, Name FROM Issues WHERE 1=0" q_iss>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mpa}, {ManagePub})
SET_ACCESS({mia}, {ManageIssue})

B_STYLE
E_STYLE

B_PBODY1

<!sql setdefault lang 0>dnl
<!sql setdefault pub 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Number, Name FROM Issues WHERE IdPublication=?pub AND IdLanguage=?lang ORDER BY Number DESC" q_iss>dnl
B_PBAR
    X_PBUTTON({X_ROOT/pub/issues/?Pub=<!sql print #pub>}, {Issues})
<!sql if $mia>dnl
    X_PBUTTON({X_ROOT/pub/issues/qadd.xql?Pub=<!sql print #pub>}, {Add new issue})
<!sql endif>dnl
<!sql if $mpa>dnl
	X_PSEP
	X_PLABEL1({Publication:})
	X_ABUTTON1({X_ROOT/pub/deftime.xql?Pub=<!sql print #pub>}, {Subscription default time})
<!sql endif>dnl
X_PSEP2
<FORM NAME="FORM_ISS" METHOD="GET">
<!sql if $NUM_ROWS>dnl
<SELECT NAME="iss" ONCHANGE="var f = this.form.iss; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/i3.xql?lang=<!sql print #lang>&amp;pub=<!sql print #pub>&amp;iss=' + v; if (v != 0) {{ parent.frames[1].location.href = x; }}">
	<OPTION VALUE="0">---Select issue---<!sql print_loop q_iss><OPTION VALUE="<!sql print ~q_iss.Number>"><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name><!sql done>
</SELECT>
<!sql else>dnl
<SELECT DISABLED><OPTION>No issues</SELECT>
<!sql endif>dnl
</FORM>
E_PBAR

E_BODY

<!sql endif>dnl

E_DATABASE
E_HTML
