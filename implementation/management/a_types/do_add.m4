INCLUDE_PHP_LIB(<*$ADMIN_DIR/a_types*>)
B_DATABASE
<?php  todef('cName');
$correct= 1;
$created= 0;
$j= 0;
?>dnl

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageArticleTypes*>)

B_HEAD
	X_TITLE(<*Adding new article type*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add article types.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Adding new article type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Article Types*>, <*a_types/*>)
E_HEADER_BUTTONS
E_HEADER

<P>
B_MSGBOX(<*Adding new article type*>)
	X_MSGBOX_TEXT(<*
<?php  if ($cName == "") {
    $correct= 0; ?>dnl
	<LI><?php  putGS('You must complete the $1 field.','</B>'.getGS('Name').'</B>'); ?></LI>
<?php  } else {
	$cName=decS($cName);

	$ok = valid_field_name($cName);
	if ($ok == 0) {
		$correct= 0; ?>dnl
		<LI><?php  putGS('The $1 field may only contain letters and underscore (_) character.', '</B>' . getGS('Name') . '</B>'); ?></LI>
    <?php  }

    $cName=encS($cName);
    if ($correct) {
	query ("SHOW TABLES LIKE 'X$cName'", 't');
	if ($NUM_ROWS) {
	    $correct= 0; ?>dnl
	<LI><?php  putGS('The article type $1 already exists.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
	<?php  }
    }
    
    if ($correct) {
	$tname = "X" . mysql_escape_string($cName);
	$sql = "CREATE TABLE `".$tname."` (NrArticle INT UNSIGNED NOT NULL, IdLanguage INT UNSIGNED NOT NULL, PRIMARY KEY(NrArticle, IdLanguage))";
	query($sql);
	$created= 1; ?>
	<LI><?php  putGS('The article type $1 has been added.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*61*>, <*getGS('The article type $1 has been added.',$cName)*>)
<?php  }
} ?>dnl
	*>)
<?php 
    todef ('Back');
    if ($correct && $created) {
		$params = array($operation_attr=>$operation_create, "article_type"=>"$cName");
		$msg = build_reset_cache_msg($cache_type_article_types, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New field*>, <*Add new field*>, <*X_ROOT/a_types/fields/add.php?AType=<?php  print encURL($cName); ?>*>)
		REDIRECT(<*New type*>, <*Add another*>, <*X_ROOT/a_types/add.php*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/a_types/*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/a_types/add.php<?php  if ($Back != "") { ?>?Back=<?php  print encURL($Back); } ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML


