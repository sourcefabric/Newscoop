INCLUDE_PHP_LIB(<*$ADMIN_DIR/pub/issues*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*DeleteIssue*>)

B_HEAD
	X_TITLE(<*Deleting issue*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to delete issues.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Pub');
    todefnum('Issue');
    todefnum('Language');
?>dnl
B_HEADER(<*Deleting issue*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Issues*>, <*pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Publications*>, <*pub/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    query ("SELECT Name FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language", 'q_iss');
    if ($NUM_ROWS) {
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    fetchRow($q_iss);
	    fetchRow($q_pub);
?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Deleting issue*>)
	X_MSGBOX_TEXT(<*
<?php 
    todefnum('del', 1);
    $NUM_ROWS = 0;
    $AFFECTED_ROWS = 0;
    query ("SELECT COUNT(*) FROM Articles WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language", 'q_art');
    fetchRowNum($q_art);
    if (getNumVar($q_art,0) != 0) {
	$del= 0; ?>dnl
	<LI><?php  putGS('There are $1 article(s) left.',getNumVar($q_art,0)); ?></LI>
    <?php  }
    
	if ($del){
		query ("SELECT IdPublication FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language LIMIT 1", 'q_sect');
		if ($NUM_ROWS) {
			query ("DELETE FROM Sections WHERE IdPublication=$Pub AND NrIssue=$Issue AND IdLanguage=$Language", 'q_sect');
	    	    	if ($AFFECTED_ROWS > 0) {?>
				<LI><?php  putGS('All sections from Issue $1 from publication $2 deleted','<B>'.getHVar($q_iss,'Name').'</B>', '<B>'.getHVar($q_pub,'Name').'</B>'); ?></LI>
					X_AUDIT(<*12*>, <*getGS('All sections from Issue $1 from publication $2 deleted',getHVar($q_iss,'Name'),getHVar($q_pub,'Name'))*>)
			<?php  } else { ?>dnl
				<LI><?php  putGS('The issue $1 could not be deleted.','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI>
				<?php  $del = 0;
			}
		}
	}

	if ($del){
		query ("DELETE FROM Issues WHERE IdPublication=$Pub AND Number=$Issue AND IdLanguage=$Language");
		if ($AFFECTED_ROWS > 0) { ?>
			<LI><?php  putGS('The issue $1 has ben deleted.','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI>
			X_AUDIT(<*12*>, <*getGS('Issue $1 from publication $2 deleted',getHVar($q_iss,'Name'),getHVar($q_pub,'Name'))*>)
		<?php  } else { ?>dnl
			<LI><?php  putGS('The issue $1 could not be deleted.','<B>'.getHVar($q_iss,'Name').'</B>'); ?></LI>
		<?php  }
	} ?>dnl
*>)
	
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/pub/issues/?Pub=<?php  pencURL($Pub); ?>*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>
<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such issue.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
