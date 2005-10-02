<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile('globals');
$localfile=selectLanguageFile("pub");
@include_once($globalfile);
@include_once($localfile);
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
?>
<?php
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
?>


<?php 
    todefnum('TOL_UserId');
    todefnum('TOL_UserKey');
    query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
    $access=($NUM_ROWS != 0);
    if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	 if ($NUM_ROWS){
	 	fetchRow($XPerm);
	 }
	 else $access = 0;						//added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	 $xpermrows= $NUM_ROWS;
    }
    else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
    }
?>
    


<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Countries Subscription Default Time"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/logout.php">
<?php  } 
    query ("SELECT * FROM SubsDefTime WHERE 1=0", 'q_deft');
    
?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php 
    todefnum('Pub');
    todefnum('Language', 1);
    
?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Countries Subscription Default Time"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);    
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

</TR></TABLE>

<TABLE>
<TR>
	<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="countryadd.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>" ><IMG SRC="/admin/img/icon/add.png" BORDER="0"></A></TD><TD><A HREF="countryadd.php?Pub=<?php  pencURL($Pub); ?>&Language=<?php  pencURL($Language); ?>" ><B><?php  putGS("Add new country"); ?></B></A></TD></TR></TABLE></TD>
	<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="edit.php?Pub=<?php  pencURL($Pub); ?>" ><IMG SRC="/admin/img/icon/back.png" BORDER="0"></A></TD><TD><A HREF="edit.php?Pub=<?php  pencURL($Pub); ?>" ><B><?php  putGS("Back to publication"); ?></B></A></TD></TR></TABLE></TD>
</TR>
</TABLE>

<P><?php 
    todefnum('ListOffs');
    if ($ListOffs < 0)
	$ListOffs= 0;
	
    query ("SELECT * FROM SubsDefTime WHERE IdPublication=$Pub ORDER BY CountryCode LIMIT $ListOffs, 11", 'q_deft');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=10;
	$color=0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Country<BR><SMALL>(click to edit)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" nowrap><B><?php  putGS("Trial Period"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" nowrap><B><?php  putGS("Paid Period"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	</TR>
<?php  
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($q_deft);
	if ($i) { ?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD >
<?php  query ("SELECT * FROM Countries WHERE Code = '".getVar($q_deft,'CountryCode')."' AND IdLanguage = $Language", 'q_ctr'); 
    fetchRow($q_ctr);
    ?>			<A HREF="/admin/pub/editdeftime.php?Pub=<?php  pencURL($Pub); ?>&CountryCode=<?php  pgetHVar($q_deft,'CountryCode'); ?>&Language=<?php  pencURL($Language); ?>"><?php  pgetHVar($q_ctr,'Name'); ?> (<?php  pgetHVar($q_ctr,'Code'); ?>)</A>
		</TD>
		<TD ALIGN="RIGHT">
			<?php  pgetHVar($q_deft,'TrialTime'); ?>
		</TD>
		<TD ALIGN="RIGHT">
			<?php  pgetHVar($q_deft,'PaidTime'); ?>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/admin/pub/deldeftime.php?Pub=<?php  pencURL($Pub); ?>&CountryCode=<?php  pgetUVar($q_deft,'CountryCode'); ?>&Language=<?php  pencURL($Language); ?>"><IMG SRC="/admin/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete entry $1',getHVar($q_deft,'CountryCode')); ?>" TITLE="<?php  putGS('Delete entry $1',getHVar($q_deft,'CountryCode')); ?>" ></A>
		</TD>
	</TR>
<?php 
    $i--;
    }
} 
?>	<TR><TD COLSPAN="2" NOWRAP>
<?php  if ($ListOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="index.php?Pub=<?php  pencURL($Pub); ?>&ListOffs=<?php  print ($ListOffs - 10); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  }
    if ($nr < 11) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="index.php?Pub=<?php  pencURL($Pub); ?>&ListOffs=<?php  print ($ListOffs + 10); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>

<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No entries defined.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  }  else { ?><BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>

