B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({AddArticle})

B_HEAD
	X_EXPIRES
	X_TITLE({Add New Article})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add articles.})
<!sql endif>dnl
<!sql query "SELECT * FROM Issues WHERE 1=0" q_iss>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

<!sql setdefault Pub 0>dnl
B_HEADER({Add New Article})
B_HEADER_BUTTONS
X_HBUTTON({Publications}, {pub/add_article.xql})
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

<P>
X_BULLET({Select the issue:})

<!sql set IssNr "xxxxxxxxx">dnl
<P><!sql setdefault IssOffs 0><!sql if $IssOffs < 0><!sql set IssOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT Name, IdLanguage, Number, Name, if(Published='Y', PublicationDate, 'No') as Pub FROM Issues WHERE IdPublication=?Pub ORDER BY Number DESC LIMIT $IssOffs, 11" q_iss>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Nr}, {1%})
		X_LIST_TH({Name<BR><SMALL>(click to select the issue)</SMALL>})
		X_LIST_TH({Language})
		X_LIST_TH({Published<BR><SMALL>(yyyy-mm-dd)</SMALL>}, {1%})
	E_LIST_HEADER

<!sql print_loop q_iss>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM({RIGHT})
	<!sql if $IssNr != @q_iss.Number>dnl
			<!sql print ~q_iss.Number>
	<!sql else>dnl
		&nbsp;
	<!sql endif>dnl
		E_LIST_ITEM
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/sections/add_article.xql?Pub=<!sql print #Pub>&Issue=<!sql print #q_iss.Number>&Language=<!sql print #q_iss.IdLanguage>"><!sql print ~q_iss.Name></A>
		E_LIST_ITEM
		B_LIST_ITEM
	<!sql query "SELECT Name FROM Languages WHERE Id=?q_iss.IdLanguage" language>dnl
			<!sql print_rows language "~language.Name">
	<!sql free language>dnl
		E_LIST_ITEM
		B_LIST_ITEM({CENTER})
			<!sql print ~q_iss.Pub>
		E_LIST_ITEM
	E_LIST_TR
<!sql setexpr IssNr @q_iss.Number>dnl
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($IssOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({add_article.xql?Pub=<!sql print #Pub>&IssOffs=<!sql eval ($IssOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({add_article.xql?Pub=<!sql print #Pub>&IssOffs=<!sql eval ($IssOffs + 10)>})
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
