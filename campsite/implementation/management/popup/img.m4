B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Images})
<!sql query "SELECT Number, Description FROM Images WHERE 1=0" q_img>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({aia}, {AddImage})
SET_ACCESS({aaa}, {AddArticle})

B_STYLE
E_STYLE

B_PBODY2

<!sql setdefault lang 0>dnl
<!sql setdefault slang 0>dnl
<!sql setdefault pub 0>dnl
<!sql setdefault iss 0>dnl
<!sql setdefault ssect 0>dnl
<!sql setdefault art 0>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Number, Description FROM Images WHERE IdPublication=?pub AND NrIssue=?iss AND NrSection=?ssect AND NrArticle=?art ORDER BY Number" q_img>dnl
B_PBAR
	X_PBUTTON({X_ROOT/pub/issues/sections/articles/images/?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Section=<!sql print #ssect>&Article=<!sql print #art>&Language=<!sql print #lang>}, {Images})
<!sql if $aia>dnl
	X_PBUTTON({X_ROOT/pub/issues/sections/articles/images/add.xql?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Section=<!sql print #ssect>&Article=<!sql print #art>&Language=<!sql print #lang>}, {Add image})
<!sql endif>dnl
	X_PSEP
	X_PLABEL2({Article:})
<!sql if $aaa>dnl
	X_ABUTTON2({X_ROOT/pub/issues/sections/articles/edit_b.xql?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Section=<!sql print #ssect>&Article=<!sql print #art>&Language=<!sql print #lang>&sLanguage=<!sql print #slang>}, {Edit})
	X_ABUTTON2({X_ROOT/pub/issues/sections/articles/edit.xql?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Section=<!sql print #ssect>&Article=<!sql print #art>&Language=<!sql print #lang>&sLanguage=<!sql print #slang>}, {Details})
<!sql endif>dnl
	X_ABUTTON2({javascript:void(window.open('X_ROOT/pub/issues/sections/articles/preview.xql?Pub=<!sql print #pub>&Issue=<!sql print #iss>&Section=<!sql print #ssect>&Article=<!sql print #art>&Language=<!sql print #lang>&sLanguage=<!sql print #slang>', 'fpreview', 'menu=no,width=620,height=460'))}, {Preview})
X_PSEP2
<FORM METHOD="GET" ACTION="X_ROOT/pub/issues/sections/articles/images/view.xql" TARGET="fmain" NAME="FORM_IMG">
<!sql if $NUM_ROWS>dnl
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
<TR><TD>
<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<!sql print #pub>">
<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<!sql print #iss>">
<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<!sql print #ssect>">
<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<!sql print #art>">
<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<!sql print #lang>">
<SELECT NAME="Image">
    <!sql print_loop q_img><OPTION VALUE="<!sql print ~q_img.Number>"><!sql print ~q_img.Description><!sql done>
</SELECT>
</TD><TD>
X_NEW_BUTTON({View}, {javascript:void(document.FORM_IMG.submit())})
</TD></TR>
</TABLE>
<!sql else>dnl
<SELECT DISABLED><OPTION>No images</SELECT>
<!sql endif>dnl
</FORM>
E_PBAR

E_BODY

<!sql endif>dnl

E_DATABASE
E_HTML
