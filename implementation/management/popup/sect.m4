B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Sections})
<!sql query "SELECT Number, Name FROM Sections WHERE 1=0" q_sect>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({msa}, {ManageSection})

B_STYLE
E_STYLE

B_PBODY2

<!sql setdefault lang 0>dnl
<!sql setdefault pub 0>dnl
<!sql setdefault iss 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Number, Name FROM Sections WHERE IdPublication=?pub AND NrIssue=?iss AND IdLanguage=?lang ORDER BY Number" q_sect>dnl
B_PBAR
	X_PBUTTON({X_ROOT/pub/issues/sections/?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Language=<!sql print #lang>}, {Sections})
<!sql if $msa>dnl
	X_PBUTTON({X_ROOT/pub/issues/sections/add.xql?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Language=<!sql print #lang>}, {Add new section})
<!sql endif>dnl
	X_PSEP
	X_PLABEL2({Issue:})
	X_ABUTTON2({javascript:void(window.open('X_ROOT/pub/issues/preview.xql?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Language=<!sql print #lang>', 'fpreview', 'menu=no,width=620,height=460'))}, {Preview})
X_PSEP2
<FORM NAME="FORM_SECT" METHOD="GET">
<!sql if $NUM_ROWS>dnl
<SELECT NAME="ssect" ONCHANGE="var f = this.form.ssect; var v = f.options[f.selectedIndex].value; var x = 'X_ROOT/popup/i4.xql?lang=<!sql print #lang>&amp;pub=<!sql print #pub>&amp;iss=<!sql print #iss>&amp;ssect=' + v; if (v != 0) {{ parent.frames[1].location.href = x; }}">
	<OPTION VALUE="0">---Select section---<!sql print_loop q_sect><OPTION VALUE="<!sql print ~q_sect.Number>"><!sql print ~q_sect.Number>. <!sql print ~q_sect.Name><!sql done>
</SELECT>
<!sql else>dnl
<SELECT DISABLED><OPTION>No sections</SELECT>
<!sql endif>dnl
</FORM>
E_PBAR

E_BODY

<!sql endif>dnl

E_DATABASE
E_HTML
