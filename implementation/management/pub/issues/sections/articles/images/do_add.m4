B_HTML
INCLUDE_PHP_LIB(<*../../../../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*AddImage*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new image*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add images*>)
<?php  } else {
    ## added by sebastian ##
    todefnum('Id');
    ########################
    dSystem( "$scriptBase/process_i '$Id'");
}
?>dnl

E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
B_HEADER(<*Adding new image*>)
B_HEADER_BUTTONS
X_HEADER_NO_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<BLOCKQUOTE>
	<LI><?php  putGS('Adding new image'); ?></LI>
X_AUDIT(<*41*>, <*getGS('Image uploaded')*>)
</BLOCKQUOTE>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
