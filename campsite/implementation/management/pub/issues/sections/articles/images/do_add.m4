B_HTML
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS({AddImage})

B_HEAD
	X_EXPIRES
	X_TITLE({Adding New Image})
<!sql if $access == 0>dnl
	X_AD({You do not have the right to add images})
<!sql else>dnl
<!sql exec "X_SCRIPT_BIN/process_i" $Id>
<!sql endif>dnl

E_HEAD

<!sql if $access>dnl
B_STYLE
E_STYLE

B_BODY
<!sql setdefault Id  0>dnl
B_HEADER({Adding New Image})
B_HEADER_BUTTONS
X_HEADER_NO_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<BLOCKQUOTE>
	<LI>Adding new image...</LI>
X_AUDIT({41}, {Image uploaded})
</BLOCKQUOTE>

X_HR
X_COPYRIGHT
E_BODY
<!sql endif>dnl

E_DATABASE
E_HTML
