INCLUDE_PHP_LIB(<*topics*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_TITLE(<*Add new topic*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add topics.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new topic*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER
<?php 
	todefnum('IdCateg');
	todef('cName');
	$correct=1;
	$created=0;
?>dnl
<P>

B_MSGBOX(<*Adding new topic*>)
	X_MSGBOX_TEXT(<*
<?php 
    $cName=trim($cName);

    if ($cName == "" || $cName == " ") {
	$correct= 0; ?>dnl
		<LI><?php  putGS('You must fill in the $1 field.','<B>'.getGS('Name').'</B>'); ?></LI>
    <?php  }

	 if ($correct) {
		$AFFECTED_ROWS=0;
		query ("UPDATE AutoId SET TopicId = LAST_INSERT_ID(TopicId + 1)");
		query ("INSERT INTO Topics SET Id = LAST_INSERT_ID(), Name='".decS($cName)."', ParentId = '$IdCateg', LanguageId = 1");
		$created= ($AFFECTED_ROWS > 0);
		$sql = "select Id from Topics where Name = '".$cName."' and LanguageId = 1";
		query($sql, 'q_topic_id');
		fetchRow($q_topic_id);
		$topic_id = getVar($q_topic_id, 'Id');
	}

	if ($created) {
		$params = array($operation_attr=>$operation_create, "tpid"=>"$topic_id");
		$msg = build_reset_cache_msg($cache_type_topics, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
?>dnl
		<LI><?php  putGS('The topic $1 has been successfuly added.',"<B>".encHTML(decS($cName))."</B>"); ?></LI>
		X_AUDIT(<*141*>, <*getGS('Topic $1 added',$cName)*>)
	<?php 
	} else {
	if ($correct != 0) { ?>dnl
		<LI><?php  putGS('The topic could not be added.'); ?></LI>
	<?php  }
}
?>dnl
		*>)
<?php  if ($correct && $created) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/topics/add.php?IdCateg=<?php p($IdCateg);?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/topics/index.php?IdCateg=<?php p($IdCateg);?>*>)
	E_MSGBOX_BUTTONS
<?php  } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/topics/add.php?IdCateg=<?php p($IdCateg);?>*>)
	E_MSGBOX_BUTTONS
<?php  } ?>dnl
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
