
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Images})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT * FROM Images WHERE 1=0" q_img>dnl
E_HEAD

<!sql if $access>dnl

SET_ACCESS({aia}, {AddImage})
SET_ACCESS({cia}, {ChangeImage})
SET_ACCESS({dia}, {DeleteImage})

B_STYLE
E_STYLE

B_BODY
<!sql setdefault Pub 0>dnl
<!sql setdefault Issue 0>dnl
<!sql setdefault Section 0>dnl
<!sql setdefault Article 0>dnl
<!sql setdefault Language 0>dnl
<!sql setdefault sLanguage 0>dnl
B_HEADER({Images})
B_HEADER_BUTTONS
X_HBUTTON({Articles}, {pub/issues/sections/articles/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>&Section=<!sql print #Section>})
X_HBUTTON({Sections}, {pub/issues/sections/?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Language=<!sql print #Language>})
X_HBUTTON({Issues}, {pub/issues/?Pub=<!sql print #Pub>})
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Articles WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND Number=?Article" q_art>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Sections WHERE IdPublication=?Pub AND NrIssue=?Issue AND IdLanguage=?Language AND Number=?Section" q_sect>dnl
<!sql if $NUM_ROWS>dnl
<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Issues WHERE IdPublication=?Pub AND Number=?Issue AND IdLanguage=?Language" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql query "SELECT * FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl

<!sql query "SELECT Name FROM Languages WHERE Id=?Language" q_lang>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
X_CURRENT({Issue:}, {<B><!sql print ~q_iss.Number>. <!sql print ~q_iss.Name> (<!sql print ~q_lang.Name>)</B>})
X_CURRENT({Section:}, {<B><!sql print ~q_sect.Number>. <!sql print ~q_sect.Name></B>})
X_CURRENT({Article:}, {<B><!sql print ~q_art.Name></B>})
E_CURRENT
<!sql free q_lang>dnl

<table>
<!sql if $aia != 0>

<tr><td>X_NEW_BUTTON({Add new image}, {add.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>})</td>
<td>X_NEW_BUTTON({Select an old image}, {select.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>})</td></tr>
<!sql endif>
<tr><td>X_NEW_BUTTON({Back to article details}, {../edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>})</td></tr>
</table>

<P><!sql setdefault ImgOffs 0><!sql if $ImgOffs < 0><!sql set ImgOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Images WHERE IdPublication=?Pub AND NrIssue=?Issue AND NrSection=?Section AND NrArticle=?Article ORDER BY Number LIMIT $ImgOffs, 11" q_img>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Nr}, {1%})
		X_LIST_TH({Click to view image}<BR>)
		X_LIST_TH({Photographer})
		X_LIST_TH({Place})
		X_LIST_TH({Date<BR><SMALL>(yyyy-mm-dd)</SMALL>})
	<!sql if $cia != 0>
		X_LIST_TH({Info}, {1%})
	<!sql endif>
	<!sql if $dia != 0>
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER
<!sql print_loop q_img>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM({RIGHT})
			<!sql print ~q_img.Number>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/articles/images/view.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Image=<!sql print #q_img.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>"><!sql print ~q_img.Description></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~q_img.Photographer>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~q_img.Place>&nbsp;
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~q_img.Date>
		E_LIST_ITEM
	<!sql if $cia != 0>
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/pub/issues/sections/articles/images/edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Image=<!sql print #q_img.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>">Change</A>
		E_LIST_ITEM
	<!sql endif>
	<!sql if $dia != 0>
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete image <!sql print ~q_img.Description>}, {icon/x.gif}, {pub/issues/sections/articles/images/del.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Image=<!sql print #q_img.Number>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>})
		E_LIST_ITEM
	<!sql endif>
	E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($ImgOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&ImgOffs=<!sql eval ($ImgOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?Pub=<!sql print #Pub>&Issue=<!sql print #Issue>&Section=<!sql print #Section>&Article=<!sql print #Article>&Language=<!sql print #Language>&sLanguage=<!sql print #sLanguage>&ImgOffs=<!sql eval ($ImgOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No images.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such issue.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such section.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such article.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
