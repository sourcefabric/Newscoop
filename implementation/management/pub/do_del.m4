B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeletePub*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Deleting publication*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete publications.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Deleting publication*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Publications*>, <*pub/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('Pub');
    todefnum('del',1);
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) { 
	    fetchRow($q_pub);
	
	?>dnl
<P>
B_MSGBOX(<*Deleting publication*>)
	X_MSGBOX_TEXT(<*
<?php 
    query ("SELECT COUNT(*) FROM Issues WHERE IdPublication=$Pub", 'q_iss');
    fetchRowNum($q_iss);
    if (getNumVar($q_iss,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 issue(s) left.',getNumVar($q_iss,0)); ?></LI>
    <?php  }
    
    query ("SELECT COUNT(*) FROM Sections WHERE IdPublication=$Pub", 'q_sect');
    fetchRowNum($q_sect);
    if (getNumVar($q_sect,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 section(s) left.',getNumVar($q_sect,0)); ?></LI>
    <?php  }
    
    query ("SELECT COUNT(*) FROM Articles WHERE IdPublication=$Pub", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 article(s) left.',getNumVar($q_art,0)); ?></LI>
    <?php  }
    
    query ("SELECT COUNT(*) FROM Subscriptions WHERE IdPublication=$Pub", 'q_subs');
    fetchRowNum($q_subs);
    if (getNumVar($q_subs,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 subscription(s) left.',getNumVar($q_subs,0)); ?></LI>
    <?php  }
    
    $AFFECTED_ROWS=0;
    
	if ($del) {
		query("DELETE FROM Aliases WHERE IdPublication = " . $Pub);
		query("DELETE FROM Publications WHERE Id = " . $Pub);
		$params = array($operation_attr=>$operation_delete, "IdPublication"=>"$Pub" );
		$msg = build_reset_cache_msg($cache_type_publications, $params);
		send_message($SERVER_ADDRESS, server_port(), $msg, $err_msg);
	}

    if ($AFFECTED_ROWS > 0) { ?>dnl
	<LI><?php  putGS('The publication $1 has been deleted.','<B>'.getHVar($q_pub,'Name').'</B>'); ?></LI>
X_AUDIT(<*2*>, <*getGS('Publication $1 deleted',getHVar($q_pub,'Name'))*>)
<?php  } else { ?>dnl
	<LI><?php  putGS('The publication $1 could not be deleted.','<B>'.getHVar($q_pub,'Name').'</B>'); ?></LI>
<?php  } ?>dnl
	*>)
	B_MSGBOX_BUTTONS
<?php  if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/pub/*>)
<?php  } else { ?>dnl
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>

X_HR
X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
