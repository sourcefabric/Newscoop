<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("$ADMIN_DIR/pub/issues");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::get('Pub', 'int', 0);
$IssOffs = Input::get('IssOffs', 'int', 0);

$mia = $User->hasPermission('ManageIssue');
$dia = $User->hasPermission('DeleteIssue');
$publish = $User->hasPermission('Publish');

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title"><?php putGS("Issues"); ?></TD>
		<TD ALIGN=RIGHT>
			<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0">
			<TR>
				<TD><A HREF="/<?php echo $ADMIN; ?>/pub/" class="breadcrumb"><?php  putGS("Publications");  ?></A></TD>
			</TR>
			</TABLE>
		</TD>
	</TR>
</TABLE>

<?php
query ("SELECT Name, IdDefaultLanguage FROM Publications WHERE Id=$Pub", 'q_pub');
if ($NUM_ROWS) {
	fetchRow($q_pub);
	$IdLang = getVar($q_pub,'IdDefaultLanguage');
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD>
	<TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>
</TR>
</TABLE>

<?php
	if ($mia != 0) {
		query ("SELECT MAX(Number) FROM Issues WHERE IdPublication=$Pub", 'q_nr');
		fetchRowNum($q_nr);
		if (getNumVar($q_nr,0) == "") {
?>
	<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="add_new.php?Pub=<?php  pencURL($Pub); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></A></TD><TD><A HREF="add_new.php?Pub=<?php  pencURL($Pub); ?>" ><B><?php  putGS("Add new issue"); ?></B></A></TD></TR></TABLE>
	<?php  } else { ?>	<P><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="qadd.php?Pub=<?php  pencURL($Pub); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add.png" BORDER="0"></A></TD><TD><A HREF="qadd.php?Pub=<?php  pencURL($Pub); ?>" ><B><?php  putGS("Add new issue"); ?></B></A></TD></TR></TABLE>
<?php  }
	}
	$IssNr= "xxxxxxxxx";
?>
<P><?php 
	if ($IssOffs < 0)
		$IssOffs= 0;
	$lpp=20;

	$sql = "SELECT Name, IdLanguage, abs(IdLanguage-$IdLang) as IdLang, Number, Name, PublicationDate, Published, ShortName FROM Issues WHERE IdPublication=$Pub ORDER BY Number DESC, IdLang ASC LIMIT $IssOffs, ".($lpp+1);
	query($sql, 'q_iss');
	if ($NUM_ROWS) {
		$nr = $NUM_ROWS;
		$i = $lpp;
		$color = 0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
	<?php  if ($mia != 0) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Nr"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to see sections)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Short Name"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Language"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Published<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></B></TD>
	<?php if ($publish) { ?>
		<TD ALIGN="center" VALIGN="TOP" width="1%"><B><?php  putGS("Automatic publishing"); ?></B></TD>
	<?php } ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Translate"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Configure"); ?></B></TD> 
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Preview"); ?></B></TD>
	<?php  } else { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Nr"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to see sections)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Language"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Published<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Preview"); ?></B></TD>
	<?php  }
	
	if ($dia != 0) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
	</TR>

<?php 
	for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_iss);
	if ($i) { ?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
<?php  if ($mia != 0) { ?>
		<TD ALIGN="RIGHT">
	<?php  if ($IssNr != getVar($q_iss,'Number'))
		pgetHVar($q_iss,'Number');
	    else
		print '&nbsp;';
	 ?>		</TD>
		<TD >
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  pgetHVar($q_iss,'Name'); ?></A>
		</TD>
		<TD >
			<?php  pgetHVar($q_iss,'ShortName'); ?>
		</TD>
		<TD >
	<?php  query ("SELECT Name FROM Languages WHERE Id=".getVar($q_iss,'IdLanguage'), 'language');
	    for($loop2=0;$loop2<$NUM_ROWS;$loop2++) {
		fetchRow($language);
		print getHVar($language,'Name');
	    } 
	 ?>		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/status.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  if (getHVar($q_iss, 'Published') == 'Y') pgetHVar($q_iss,'PublicationDate'); else print putGS("Publish"); ?></A>
		</TD>
	<?php if ($publish) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/autopublish.php?Pub=<?php pencURL($Pub); ?>&Issue=<?php pgetUVar($q_iss,'Number'); ?>&Language=<?php pgetUVar($q_iss,'IdLanguage'); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/automatic_publishing.png" alt="<?php putGS("Automatic publishing"); ?>" border="0"></A>
		</TD>
	<?php } ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/translate.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/translate.png" alt="<?php  putGS("Translate"); ?>" border="0"></A>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/edit.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/configure.png" alt="<?php  putGS("Configure"); ?>" border="0"></A>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/pub/issues/preview.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>', 'fpreview', 'resizable=yes, menubar=yes, toolbar=yes, width=680, height=560'); return false"><img src="/<?php echo $ADMIN; ?>/img/icon/preview.png" alt="<?php  putGS("Preview"); ?>" border="0"></A>
		</TD>
<?php  } else { ?>
		<TD ALIGN="RIGHT">
	<?php pgetHVar($q_iss,'Number'); ?>		</TD>
		<TD >
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  pgetHVar($q_iss,'Name'); ?></A>
		</TD>
		<TD >
	<?php
		query ("SELECT Name FROM Languages WHERE Id=".getVar($q_iss,'IdLanguage'), 'language');
		for($loop2=0;$loop2<$NUM_ROWS;$loop2++) {
			fetchRow($language);
			print getHVar($language,'Name');
		}
	?>		</TD>
		<TD ALIGN="CENTER">
			<?php  if (getHVar($q_iss, 'Published') == 'Y') pgetHVar($q_iss,'PublicationDate'); else print putGS("No"); ?>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/pub/issues/preview.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>', 'fpreview', 'resizable=yes, menubar=yes, toolbar=yes, width=680, height=560'); return false"><?php  putGS("Preview"); ?></A>
		</TD>
<?php  }
    if ($dia != 0) { ?> 
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/del.php?Pub=<?php  pencURL($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete issue $1',getHVar($q_iss,'Name')); ?>"></A>
		</TD>
<?php  } ?>
	</TR>
<?php 
    $IssNr=getVar($q_iss,'Number');
    $i--;
    }
}

?>	<TR><TD COLSPAN="2" NOWRAP>
<?php  if ($IssOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="index.php?Pub=<?php  pencURL($Pub); ?>&IssOffs=<?php  print ($IssOffs - $lpp); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  }
    
    if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="index.php?Pub=<?php  pencURL($Pub); ?>&IssOffs=<?php  print ($IssOffs + $lpp); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No issues.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No such publication.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>

</HTML>
