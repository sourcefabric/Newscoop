B_HTML
B_DATABASE

CHECK_BASIC_ACCESS

B_HEAD
	X_EXPIRES
	X_TITLE({Issues})
<!sql if $access == 0>dnl
	X_LOGOUT
<!sql endif>dnl
<!sql query "SELECT * FROM Issues WHERE 1=0" q_iss>dnl
E_HEAD

<!sql if $access>dnl
SET_ACCESS({mia}, {ManageIssue})
SET_ACCESS({dia}, {DeleteIssue})

B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
B_HEADER({Issues})
B_HEADER_BUTTONS
X_HBUTTON({Publications}, {pub/})
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name FROM Publications WHERE Id=?Pub" q_pub>dnl
<!sql if $NUM_ROWS>dnl
B_CURRENT
X_CURRENT({Publication:}, {<B><!sql print ~q_pub.Name></B>})
E_CURRENT

<!sql if $mia != 0>
	<!sql query "SELECT MAX(Number) FROM Issues WHERE IdPublication=?Pub" q_nr>dnl
	<!sql if @q_nr.0 == "">dnl
	<P>X_NEW_BUTTON({Add new issue}, {add_new.xql?Pub=<!sql print #Pub>})
	<!sql else>dnl
	<P>X_NEW_BUTTON({Add new issue}, {qadd.xql?Pub=<!sql print #Pub>})
	<!sql endif>dnl
<!sql endif>

<!sql set IssNr "xxxxxxxxx">dnl
<P><!sql setdefault IssOffs 0><!sql if $IssOffs < 0><!sql set IssOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name, IdLanguage, Number, Name, if(Published='Y', PublicationDate, if($mia, 'Publish', 'No')) as Pub, FrontPage, SingleArticle  FROM Issues WHERE IdPublication=?Pub ORDER BY Number DESC LIMIT $IssOffs, 11" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
	<!sql if $mia != 0>
		X_LIST_TH({Nr}, {1%})
		X_LIST_TH({Name<BR><SMALL>(click to see sections)</SMALL>})
		X_LIST_TH({Language})
		X_LIST_TH({Front Page Template<BR><SMALL>(click to change)</SMALL>})
		X_LIST_TH({Single Article Template<BR><SMALL>(click to change)</SMALL>})
		X_LIST_TH({Published<BR><SMALL>(yyyy-mm-dd)</SMALL>}, {1%})
		X_LIST_TH({Translate}, {1%})
		X_LIST_TH({Change}, {1%}) 
		X_LIST_TH({Preview}, {1%})
	<!sql else>
		X_LIST_TH({Nr}, {1%})
		X_LIST_TH({Name<BR><SMALL>(click to see sections)</SMALL>})
		X_LIST_TH({Language})
		X_LIST_TH({Published<BR><SMALL>(yyyy-mm-dd)</SMALL>}, {1%})
		X_LIST_TH({Preview}, {1%})
	<!sql endif>
	<!sql if $dia != 0>
		X_LIST_TH({Delete}, {1%})
	<!sql endif>
	E_LIST_HEADER

<!sql print_loop q_iss>dnl
<!sql if $i>dnl
	B_LIST_TR
<!sql if $mia != 0>
		B_LIST_ITEM({RIGHT})
	<!sql if $IssNr != @q_iss.Number>dnl
			<!sql print ~q_iss.Number>
	<!sql else>dnl
		&nbsp;
	<!sql endif>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/?Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>"><!sql print ~q_iss.Name></A>
		E_LIST_ITEM
		B_LIST_ITEM
	<!sql query "SELECT Name FROM Languages WHERE Id=?q_iss.IdLanguage" language>dnl
			<!sql print_rows language "~language.Name">
	<!sql free language>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="LOOK_PATH/?What=1&Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>"><!sql if $q_iss.FrontPage != ""><!sql print ~q_iss.FrontPage><!sql else>Click here to set...<!sql endif></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="LOOK_PATH/?What=2&Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>"><!sql if $q_iss.SingleArticle != ""><!sql print ~q_iss.SingleArticle><!sql else>Click here to set...<!sql endif></A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/pub/issues/status.xql?Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>"><!sql print ~q_iss.Pub></A>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
	<!sql if $IssNr == @q_iss.Number>dnl
			&nbsp;
	<!sql else>dnl
			<A HREF="X_ROOT/pub/issues/translate.xql?Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>">Translate</A>
	<!sql endif>dnl
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<A HREF="X_ROOT/pub/issues/edit.xql?Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>">Change</A>
                E_LIST_ITEM 		B_LIST_ITEM({CENTER})
			<A HREF="javascript:void(window.open('X_ROOT/pub/issues/preview.xql?Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>', 'fpreview', 'resizable=yes,scrollbars=yes,toolbar=yes,width=680,height=560'))">Preview</A>
		E_LIST_ITEM
<!sql else>
		B_LIST_ITEM({RIGHT})
	<!sql if $IssNr != @q_iss.Number>dnl
			<!sql print ~q_iss.Number>
	<!sql else>dnl
		&nbsp;
	<!sql endif>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/?Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>"><!sql print ~q_iss.Name></A>
		E_LIST_ITEM
		B_LIST_ITEM
	<!sql query "SELECT Name FROM Languages WHERE Id=?q_iss.IdLanguage" language>dnl
			<!sql print_rows language "~language.Name">
	<!sql free language>dnl
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<!sql print ~q_iss.Pub>
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<A HREF="javascript:void(window.open('X_ROOT/pub/issues/preview.xql?Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>', 'fpreview', 'resizable=yes,scrollbars=yes,toolbar=yes,width=680,height=560'))">Preview</A>
		E_LIST_ITEM
<!sql endif>	
<!sql if $dia != 0> 
		B_LIST_ITEM({CENTER})
			X_BUTTON({Delete issue <!sql print ~q_iss.Name>}, {icon/x.gif}, {pub/issues/del.xql?Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>})
		E_LIST_ITEM
<!sql endif>
	E_LIST_TR
<!sql setexpr IssNr @q_iss.Number>dnl
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($IssOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({index.xql?Pub=<!sql print #Pub>&IssOffs=<!sql eval ($IssOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({index.xql?Pub=<!sql print #Pub>&IssOffs=<!sql eval ($IssOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No issues.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

<!sql else>dnl
<BLOCKQUOTE>
	<LI>No such publication.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
