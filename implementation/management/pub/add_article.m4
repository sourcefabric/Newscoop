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
<!sql query "SELECT * FROM Publications WHERE 1=0" publ>dnl
E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER({Add New Article})
B_HEADER_BUTTONS
X_HBUTTON({Home}, {home.xql})
X_HBUTTON({Logout}, {logout.xql})
E_HEADER_BUTTONS
E_HEADER

<P>
X_BULLET({Select the publication:})

<P><!sql setdefault PubOffs 0><!sql if $PubOffs < 0><!sql set PubOffs 0><!sql endif><!sql set NUM_ROWS 0>dnl
<!sql query "SELECT * FROM Publications ORDER BY Name LIMIT $PubOffs, 11" publ>dnl
<!sql if $NUM_ROWS>dnl
<!sql set nr $NUM_ROWS>dnl
<!sql set i 10>dnl
<!sql set color 0>dnl
B_LIST
	B_LIST_HEADER
		X_LIST_TH({Name<BR><SMALL>(click to select the publication)</SMALL>})
		X_LIST_TH({Site}, {20%})
	E_LIST_HEADER
<!sql print_loop publ>dnl
<!sql if $i>dnl
	B_LIST_TR
		B_LIST_ITEM
			<A HREF="X_ROOT/pub/issues/add_article.xql?Pub=<!sql print #publ.Id>"><!sql print ~publ.Name></A>
		E_LIST_ITEM
		B_LIST_ITEM
			<!sql print ~publ.Site>&nbsp;
		E_LIST_ITEM
    E_LIST_TR
<!sql setexpr i ($i - 1)>dnl
<!sql endif>dnl
<!sql done>dnl
	B_LIST_FOOTER
<!sql if ($PubOffs <= 0)>dnl
		X_PREV_I
<!sql else>dnl
		X_PREV_A({add_article.xql?PubOffs=<!sql eval ($PubOffs - 10)>})
<!sql endif>dnl
<!sql if $nr < 11>dnl
		X_NEXT_I
<!sql else>dnl
		X_NEXT_A({add_article.xql?PubOffs=<!sql eval ($PubOffs + 10)>})
<!sql endif>dnl
	E_LIST_FOOTER
E_LIST
<!sql else>dnl
<BLOCKQUOTE>
	<LI>No publications.</LI>
</BLOCKQUOTE>
<!sql endif>dnl

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
