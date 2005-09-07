INCLUDE_PHP_LIB(<*$ADMIN_DIR/a_types/fields*>)
B_DATABASE
<?php 
    todef('cName');
    todef('cType');
    todef('AType');

?>dnl

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageArticleTypes*>)

B_HEAD
	X_TITLE(<*Adding new field*>)
	<?php  if ($access == 0) { ?>
		X_AD(<*You do not have the right to add article type fields.*>)
	<?php  } ?>
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY
<?php  todef('cName'); ?>dnl

B_HEADER(<*Adding new field*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Fields*>, <*a_types/fields/?AType=<?php  print encURL($AType); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Article Types*>, <*a_types/*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Article type*>, <*<?php  print encHTML($AType); ?>*>)
E_CURRENT

<?php  $created= 0; ?>dnl
<P>
B_MSGBOX(<*Adding new field*>)
	X_MSGBOX_TEXT(<*
<?php 
    $correct = valid_field_name($cName);

    if ($correct) {
	query ("SHOW FIELDS FROM X$AType LIKE 'F$cName'", 'f');
	if ($NUM_ROWS) { ?>dnl
	<LI><?php  putGS('The field $1 already exists.','<B>'.encHTML($cName).'</B>'); ?></LI>
	<?php  $correct= 0;
        }
    } else { ?>dnl
	<LI><?php  putGS('The $1  must not be void and may only contain letters and underscore (_) character.','<B>'.getGS('Name').'</B>'); ?></LI>
    <?php  } 

    if ($correct) {
	if ($cType == 1) {
	    query ("ALTER TABLE X$AType ADD COLUMN F$cName VARCHAR(255) NOT NULL");
	    $created= 1;
	} elseif ($cType == 2) {
	    query ("ALTER TABLE X$AType ADD COLUMN F$cName DATE NOT NULL");
	    $created= 1;
	} elseif ($cType == 3) {
	    query ("ALTER TABLE X$AType ADD COLUMN F$cName MEDIUMBLOB NOT NULL");
	    $created= 1;
	} else { ?>dnl
	<LI><?php  putGS('Invalid field type.'); ?></LI>
	<?php  $correct= 0;
	}
    }

    if ($created) {
		$params = array($operation_attr=>$operation_modify, "article_type"=>"$AType");
		$msg = build_reset_cache_msg($cache_type_article_types, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
?>dnl
	<LI><?php  putGS('The field $1 has been created.','<B>'.encHTML(decS($cName)).'</B>'); ?></LI>
X_AUDIT(<*71*>, <*getGS('Article type field $1 created', decS($cName))*>)
<?php  } ?>dnl
	*>)
<?php  if ($created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*Add*>, <*Add another*>, <*X_ROOT/a_types/fields/add.php?AType=<?php  print encURL($AType); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/a_types/fields/?AType=<?php  print encURL($AType); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*Ok*>, <*OK*>, <*X_ROOT/a_types/fields/?AType=<?php  print encURL($AType); ?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
