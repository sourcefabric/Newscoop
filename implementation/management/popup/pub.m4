B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Publications})
<!sql query "SELECT Id, Name FROM Publications WHERE 1=0" q_pub>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mpa}, {ManagePub})

B_STYLE
E_STYLE

B_PBODY2

<!sql setdefault lang 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Id, Name FROM Publications ORDER BY Name" q_pub>dnl
B_PBAR
    X_PBUTTON({X_ROOT/pub/}, {Publications})
<!sql if $mpa>dnl
    X_PBUTTON({X_ROOT/pub/add.xql}, {Add new publication})
<!sql endif>dnl
X_PSEP2
<FORM NAME="FORM_PUB" METHOD="GET">
<!sql if $NUM_ROWS>dnl
<SELECT NAME="pub" ONCHANGE="f = this.form.pub; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/i2.xql?lang=<!sql print #lang>&pub=' + v; if (v != 0) {{ parent.frames[1].location.href = x; }}">
	<OPTION VALUE="0">---Select publication---<!sql print_loop q_pub><OPTION VALUE="<!sql print ~q_pub.Id>"><!sql print ~q_pub.Name><!sql done>
</SELECT>
<!sql else>dnl
<SELECT DISABLED><OPTION>No publications</SELECT>
<!sql endif>dnl
</FORM>
E_PBAR

E_BODY

<!sql endif>dnl

E_DATABASE
E_HTML
