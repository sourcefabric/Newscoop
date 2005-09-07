INCLUDE_PHP_LIB(<*$ADMIN_DIR/topics*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageTopics*>)

B_HEAD
	X_TITLE(<*Deleting topic*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete topics.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting topic*>)
B_HEADER_BUTTONS
E_HEADER_BUTTONS
E_HEADER

<?php 
	todefnum('IdCateg');
	todefnum('DelCateg');
	todefnum('del',1);
	query ("SELECT Name FROM Topics WHERE Id=$DelCateg AND LanguageId = 1", 'q_cat');
	if ($NUM_ROWS) {
	    fetchRow($q_cat);

	?>dnl
<P>
B_MSGBOX(<*Deleting topic*>)
	X_MSGBOX_TEXT(<*
<?php 
	query ("SELECT COUNT(*) FROM Topics WHERE ParentId=$DelCateg AND LanguageId = 1", 'q_sons');
	fetchRowNum($q_sons);
	if (getNumVar($q_sons,0) != 0) {
		$del= 0; ?>dnl
		<LI><?php  putGS('There are $1 subtopics left.',getNumVar($q_sons,0)); ?></LI>
    <?php  }
	query ("SELECT COUNT(*) FROM ArticleTopics WHERE TopicId=$DelCateg", 'q_tart');
	fetchRowNum($q_tart);
	if (getNumVar($q_tart,0) != 0) {
		$del= 0; ?>dnl
		<LI><?php  putGS('There are $1 articles using the topic.',getNumVar($q_tart,0)); ?></LI>
    <?php  }

    $AFFECTED_ROWS=0;

    if ($del)
	query ("DELETE FROM Topics WHERE Id=$DelCateg");

	if ($AFFECTED_ROWS > 0) {
		$params = array($operation_attr=>$operation_delete, "tpid"=>"$DelCateg");
		$msg = build_reset_cache_msg($cache_type_topics, $params);
		send_message("127.0.0.1", server_port(), $msg, $err_msg);
?>dnl
		<LI><?php  putGS('The topic $1 has been deleted.','<B>'.getHVar($q_cat,'Name').'</B>'); ?></LI>
		X_AUDIT(<*142*>, <*getGS('Topic $1 deleted',getHVar($q_cat,'Name'))*>)
	<?php  } else { ?>dnl
		<LI><?php  putGS('The topic $1 could not be deleted.','<B>'.getHVar($q_cat,'Name').'</B>'); ?></LI>
	<?php  } ?>dnl
*>)

	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/topics/index.php?IdCateg=<?php p($IdCateg);?>*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/topics/index.php?IdCateg=<?php p($IdCateg);?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such topic.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
