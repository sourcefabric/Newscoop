<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR/pub","locals");
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

    <TITLE><?php  putGS("Publications"); ?></TITLE>
<?php  if ($access == 0) { ?>    <META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/logout.php">
<?php  }
    query ("SELECT * FROM Publications WHERE 1=0", 'publ');
    query("SELECT  Id as IdLang FROM Languages WHERE code='$_COOKIE[TOL_Language]'", 'q_lang');
    if($NUM_ROWS == 0){
        query("SELECT IdDefaultLanguage as IdLang  FROM Publications WHERE Id=1", 'q_lang');
    }
    fetchRow($q_lang);
    $IdLang = getVar($q_lang,'IdLang');

?></HEAD>

<?php  if ($access) {

   if (getVar($XPerm,'ManagePub') == "Y")
	$mpa=1;
    else 
	$mpa=0;
    

   if (getVar($XPerm,'DeletePub') == "Y")
	$dpa=1;
    else 
	$dpa=0;
    
?>
 
 

<BODY >

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("Publications"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR></TR></TABLE></TD></TR>
</TABLE>

<?php  if ($mpa != 0) { ?>    <P>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
    <TR>
    	<TD>
    		<A HREF="add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A>
    	</TD>
    	<TD>
    		<A HREF="add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><B><?php  putGS("Add new publication"); ?></B></A>
    	</TD>
    </TR>
    </TABLE>
<?php  } ?>
<P><?php
    todefnum('PubOffs');
    if ($PubOffs < 0) $PubOffs= 0;
    $lpp=20;

	$sql = "SELECT p.Id, p.Name, a.Name as Alias, u.Name as URLType, l.OrigName FROM "
	     . "Publications as p, Aliases as a, URLTypes as u, Languages as l WHERE "
	     . "p.IdDefaultAlias = a.Id AND p.IdURLType = u.Id AND p.IdDefaultLanguage = l.Id "
	     . "ORDER BY p.Name ASC LIMIT $PubOffs, ".($lpp+1);
	query($sql, 'publ');
    if ($NUM_ROWS) {
    $nr= $NUM_ROWS;
    $i= $lpp;
    $color= 0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list">
    <TR class="table_list_header">
        <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Name<BR><SMALL>(click to see issues)</SMALL>"); ?></B></TD>
        <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Default Site Alias"); ?></B></TD>
        <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Default Language"); ?></B></TD>
    <?php  if ($mpa != 0) { ?>        <TD ALIGN="LEFT" VALIGN="TOP" WIDTH="20%" ><B><?php  putGS("URL Type"); ?></B></TD>
        <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Configure"); ?></B></TD>
    <?php  }
    if ($dpa != 0) { ?>        <TD ALIGN="LEFT" VALIGN="TOP"><B><?php  putGS("Delete"); ?></B></TD>
    <?php  } ?>    </TR>
<?php
    for($loop=0;$loop<$nr;$loop++) {
    fetchRow($publ);
    if ($i) { ?>    <TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
        <TD >
            <A HREF="/admin/issues/?Pub=<?php  pgetUVar($publ,'Id'); ?>"><?php  pgetHVar($publ,'Name'); ?></A>
        </TD>
        <TD >
            <?php  pgetHVar($publ,'Alias'); ?>&nbsp;
        </TD>
        <TD >
            <?php  pgetHVar($publ,'OrigName'); ?>&nbsp;
        </TD>
<?php  if ($mpa != 0) { ?>        <TD >
            <?php  pgetHVar($publ,'URLType'); ?>&nbsp;
        </TD>
        <TD ALIGN="CENTER">
            <A HREF="/<?php p($ADMIN); ?>/pub/edit.php?Pub=<?php  pgetUVar($publ,'Id'); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/configure.png" alt="<?php  putGS("Configure"); ?>" title="<?php  putGS("Configure"); ?>"  border="0"></A>
        </TD>
<?php  }
    if ($dpa != 0) { ?>        <TD ALIGN="CENTER">
            <A HREF="/<?php p($ADMIN); ?>/pub/del.php?Pub=<?php  pgetVar($publ,'Id'); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" BORDER="0" ALT="<?php  putGS('Delete publication $1',getHVar($publ,'Name')); ?>" TITLE="<?php  putGS('Delete publication $1',getHVar($publ,'Name')); ?>" ></A>
        </TD>
<?php  } ?>    </TR>
<?php
    $i--;
    }
} ?>    <TR><TD COLSPAN="2" NOWRAP>
<?php  if ($PubOffs <= 0) { ?>        &lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>        <B><A HREF="index.php?PubOffs=<?php  print ($PubOffs - $lpp); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  } ?><?php  if ($nr < $lpp+1) { ?>         | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>         | <B><A HREF="index.php?PubOffs=<?php  print ($PubOffs + $lpp); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>    </TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
    <LI><?php  putGS('No publications.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
</BODY>
<?php  } ?>

</HTML>
