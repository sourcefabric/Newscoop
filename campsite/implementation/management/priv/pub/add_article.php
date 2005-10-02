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
    


    <?php  if ($access) {
	query ("SELECT AddArticle FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'Perm');
	 if ($NUM_ROWS) {
		fetchRow($Perm);
		$access = (getVar($Perm,'AddArticle') == "Y");
	}
	else $access = 0;
    } ?>
    
 

<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

	<TITLE><?php  putGS("Add new article"); ?></TITLE>
<?php  if ($access == 0) { ?>	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/ad.php?ADReason=<?php  print encURL(getGS("You do not have the right to add articles." )); ?>">
<?php  }
    query ("SELECT * FROM Publications WHERE 1=0", 'publ');
?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Add new article"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR></TR></TABLE></TD></TR>
</TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><IMG SRC="/admin/img/tol.gif" BORDER="0"></TD><TD><?php  putGS("Select the publication"); ?></TD></TR></TABLE>

<P><?php 
    todefnum('PubOffs');
    if ($PubOffs < 0)
	$PubOffs= 0;

    query ("SELECT * FROM Publications ORDER BY Name LIMIT $PubOffs, 11", 'publ');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=10;
	$color=0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Name<BR><SMALL>(click to select the publication)</SMALL>"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="20%" ><B><?php  putGS("Site"); ?></B></TD>
	</TR>
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($publ);
	if ($i) { ?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD >
			<A HREF="/admin/issues/add_article.php?Pub=<?php  pgetUVar($publ,'Id'); ?>"><?php  pgetHVar($publ,'Name'); ?></A>
		</TD>
		<TD >
			<?php  pgetHVar($publ,'Site'); ?>&nbsp;
		</TD>
    </TR>
<?php 
    $i--;
    }
}
?>	<TR><TD COLSPAN="2" NOWRAP>
<?php 
    if ($PubOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="add_article.php?PubOffs=<?php  print  ($PubOffs - 10); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  }
    if ($nr < 11) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="add_article.php?PubOffs=<?php  print  ($PubOffs + 10); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No publications.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
