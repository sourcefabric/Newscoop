B_HTML

B_HEAD
    <TITLE>None</TITLE>
E_HEAD

B_STYLE
E_STYLE

<!sql setdefault bg 0>dnl
<!sql if $bg>dnl
B_PBODY1
<!sql else>dnl
B_PBODY2
<!sql endif>dnl
E_PBODY

E_HTML
