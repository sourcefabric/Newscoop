B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_HEAD
    <TITLE>None</TITLE>
E_HEAD

B_STYLE
E_STYLE

<? 
    todefnum('bg');
    if ($bg) { ?>dnl
B_PBODY1
<? } else { ?>dnl
B_PBODY2
<? } ?>dnl
E_PBODY

E_HTML
