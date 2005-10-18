INCLUDE_PHP_LIB(<*article_types*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteArticleTypes*>)

B_HEAD
	X_TITLE(<*Deleting article type*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete article types.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting article type*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Article Types*>, <*a_types/*>)
E_HEADER_BUTTONS
E_HEADER

<?php  todef('AType'); ?>dnl

<P>
B_MSGBOX(<*Deleting article type*>)
	X_MSGBOX_TEXT(<*
<?php 
    $del= 1;
    query ("SELECT COUNT(*) FROM Articles WHERE Type='$AType'", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 article(s) left.',encHTML(getNumVar($q_art,0))); ?></LI>
    <?php  }

    if ($del) {
		$sql = "DROP TABLE X$AType";
		query($sql);
		$params = array($operation_attr=>$operation_delete, "article_type"=>"$AType");
		$msg = build_reset_cache_msg($cache_type_article_types, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
	}
    if ($del) { ?>dnl
	<LI><?php  putGS('The article type $1 has been deleted.','<B>'.encHTML($AType).'</B>'); ?></LI>
X_AUDIT(<*62*>, <*getGS('The article type $1 has been deleted.',$AType)*>)
<?php  } else { ?>dnl
	<LI><?php  putGS('The article type $1 could not be deleted.','<B>'.encHTML($AType).'</B>'); ?></LI>
<?php  } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($del) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/a_types/*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/a_types/*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

