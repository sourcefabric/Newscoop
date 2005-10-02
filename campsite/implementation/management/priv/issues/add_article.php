<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile('globals');
$localfile=selectLanguageFile("issues");
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
    
    query ("SELECT * FROM Issues WHERE 1=0", 'q_iss');
?></HEAD>

<?php  if ($access) { ?> 
 

<BODY >

<?php  todefnum('Pub'); ?><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Add new article"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR><TD><A HREF="/admin/pub/add_article.php" class="breadcrumb" ><?php  putGS("Publications");  ?></A></TD>
</TR></TABLE></TD></TR>
</TABLE>

<?php 
    query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
    if ($NUM_ROWS) { 
	fetchRow($q_pub);
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" class="current_location_table"><TR>
<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" class="current_location_title">&nbsp;<?php  putGS("Publication"); ?>:</TD><TD VALIGN="TOP" class="current_location_content"><?php  pgetHVar($q_pub,'Name'); ?></TD>

</TR></TABLE>

<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><IMG SRC="/admin/img/tol.gif" BORDER="0"></TD><TD><?php  putGS("Select the issue"); ?></TD></TR></TABLE>

<?php  $IssNr= "xxxxxxxxx"; ?><P><?php 
    todefnum('IssOffs');
    if ($IssOffs < 0)
	$IssOffs= 0;
        $lpp=10;
    
    query ("SELECT Name, IdLanguage, Number, Name, if(Published='Y', PublicationDate, 'No') as Pub FROM Issues  WHERE IdPublication=$Pub ORDER BY Number DESC LIMIT $IssOffs, ".($lpp+1), 'q_iss');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i= $lpp;
                        if($nr < $lpp) $i = $nr;
	$color= 0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Nr"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Name"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Language"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Published<BR><SMALL>(yyyy-mm-dd)</SMALL>"); ?></B></TD>
	</TR>

<?php 
    for($loop=0;$loop<$i;$loop++) {
	fetchRow($q_iss); ?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD ALIGN="RIGHT">
 <?php  if ($IssNr != getVar($q_iss,'Number')) {
		pgetHVar($q_iss,'Number');
	    }
	    else {
		p('&nbsp;');
	    }
	?>		</TD>
		<TD >
			<A HREF="/admin/sections/add_article.php?Pub=<?php  p($Pub); ?>&Issue=<?php  pgetUVar($q_iss,'Number'); ?>&Language=<?php  pgetUVar($q_iss,'IdLanguage'); ?>"><?php  pgetHVar($q_iss,'Name'); ?></A>
		</TD>
		<TD >
 <?php 
	    query ("SELECT Name FROM Languages WHERE Id=".getSVar($q_iss,'IdLanguage'), 'language');
	    $nr2=$NUM_ROWS;
	    for($loop2=0;$loop2<$nr2;$loop2++) {
		fetchRow($language);
		pgetHVar($language,'Name');
	    }
	    ?>		</TD>
		<TD ALIGN="CENTER">
			<?php  pgetHVar($q_iss,'Pub'); ?>
		</TD>
	</TR>
<?php 
    $IssNr=getVar($q_iss,'Number');
}
?>	<TR><TD COLSPAN="2" NOWRAP>
<?php  if ($IssOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="add_article.php?Pub=<?php  p($Pub); ?>&IssOffs=<?php  p($IssOffs - $lpp); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  }

    if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="add_article.php?Pub=<?php  p($Pub); ?>&IssOffs=<?php  p($IssOffs + $lpp); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No issues.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
