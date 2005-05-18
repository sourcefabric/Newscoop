<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
$globalfile=selectLanguageFile($Campsite['HTML_DIR'] . "/$ADMIN_DIR",'globals');
$localfile=selectLanguageFile("$ADMIN_DIR/users","locals");
@include_once($globalfile);
@include_once($localfile);
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
    
todefnum('TOL_UserId');
todefnum('TOL_UserKey');
query ("SELECT * FROM Users WHERE Id=$TOL_UserId AND KeyId=$TOL_UserKey", 'Usr');
$access=($NUM_ROWS != 0);
if ($NUM_ROWS) {
	fetchRow($Usr);
	query ("SELECT * FROM UserPerm WHERE IdUser=".getVar($Usr,'Id'), 'XPerm');
	if ($NUM_ROWS){
		fetchRow($XPerm);
	} else
		$access = 0; //added lately; a non-admin can enter the administration area; he exists but doesn't have ANY rights
	$xpermrows= $NUM_ROWS;
} else {
	query ("SELECT * FROM UserPerm WHERE 1=0", 'XPerm');
}

?>

<HEAD>
	<LINK rel="stylesheet" type="text/css" href="<?php echo $Campsite['WEBSITE_URL']; ?>/css/admin_stylesheet.css">

<?php
if ($access == 0) { ?>
	<META HTTP-EQUIV="Refresh" CONTENT="0; URL=/admin/logout.php">
<?php
	query ("SELECT * FROM Users WHERE 1=0", 'Users');
}
?>
</HEAD>

<?php
if ($access) {

	if (getVar($XPerm,'ManageUsers') == "Y")
		$mua = 1;
	else 
		$mua = 0;

if (getVar($XPerm,'DeleteUsers') == "Y")
		$dua = 1;
	else 
		$dua = 0;

if (getVar($XPerm,'ManageSubscriptions') == "Y")
		$msa = 1;
	else
		$msa = 0;

todef('sUname');
todef('sType');
?>

<BODY >

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" WIDTH="100%" class="page_title_container">
	<TR>
		<TD class="page_title">
		    <?php  putGS("User management"); ?>
		</TD>

	<TD ALIGN=RIGHT><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="0"><TR></TR></TABLE></TD></TR>
</TABLE>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<?php  if ($mua != 0) { ?>
	<TD><TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1"><TR><TD><A HREF="add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>" ><IMG SRC="/admin/img/icon/add.png" BORDER="0"></A></TD><TD><A HREF="add.php?Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>" ><B><?php  putGS("Add new user account"); ?></B></A></TD></TR></TABLE></TD>
	<?php  } ?>
	<TD ALIGN="RIGHT">
	<FORM METHOD="GET" ACTION="index.php" NAME="">
	<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="search_dialog">
	<TR>
		<TD><?php  putGS('User name'); ?>:</TD>
		<TD><INPUT TYPE="TEXT" class="input_text" NAME="sUname" VALUE="<?php  pencHTML($sUname); ?>" SIZE="16" MAXLENGTH="32"></TD>
		<TD><SELECT NAME="sType" class="input_select"><OPTION><OPTION VALUE="Y" <?php  if ($sType == "Y") { ?>SELECTED<?php  } ?>><?php  putGS('Reader'); ?><OPTION VALUE="N" <?php  if ($sType == "N") { ?>SELECTED<?php  } ?>><?php  putGS('Staff'); ?></SELECT></TD>
		<TD><INPUT TYPE="submit" class="button" NAME="Search" VALUE="<?php  putGS('Search'); ?>"></TD>
	</TR>
	</TABLE>
</FORM>
	</TD>
</TABLE>

<P><?php 
    todefnum('UserOffs');
    if ($UserOffs < 0) $UserOffs= 0;
    todefnum('lpp', 20);

    query ("SELECT * FROM Users WHERE Name LIKE '%$sUname%' AND Reader LIKE '$sType%' ORDER BY Name ASC LIMIT $UserOffs, ".($lpp+1), 'Users');
    if ($NUM_ROWS) {
	$nr= $NUM_ROWS;
	$i=$lpp;
	$color= 0;
?><TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("Full Name"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP"  ><B><?php  putGS("User Name"); ?></B></TD>
	<?php  if ($mua != 0) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" nowrap><B><?php  putGS("IP Access"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Password"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Reader"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Info"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Rights"); ?></B></TD>
	<?php  } else { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Reader"); ?></B></TD>
	<?php  }
	    if ($dua != 0) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" ><B><?php  putGS("Delete"); ?></B></TD>
	<?php  } ?>
	</TR>
<?php 
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($Users);
	if ($i) { ?>	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD >
			<?php  pgetHVar($Users,'Name'); ?>&nbsp;
		</TD>
		<TD >
			<?php  pgetHVar($Users,'UName'); ?>&nbsp;
		</TD>
	<?php  if ($mua != 0) {
		query ("SELECT COUNT(*) FROM SubsByIP WHERE IdUser=".getSVar($Users,'Id'), 'bip');
		fetchRowNum($bip);
		?>
		<TD ALIGN="CENTER">
                        <A HREF="/admin/users/ipaccesslist.php?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  if (getNumVar($bip,0)) putGS('Update'); else putGS('Set'); ?></A>
		</TD>
		<TD ALIGN="CENTER">
        		<A HREF="/admin/users/passwd.php?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  putGS('Password'); ?></A>
		</TD>
		<TD ALIGN="CENTER">
			<?php  if (getVar($Users,'Reader') == "Y") putGS('Yes'); else putGS('No'); ?>
		</TD>
		<TD ALIGN="CENTER">
			<A HREF="/admin/users/info.php?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  putGS('Change'); ?></A>
		</TD>
		<TD ALIGN="CENTER">
<?php  
    if (getVar($Users,'Reader') == "Y") {
		if ($msa != 0) { ?>
			<A HREF="/admin/users/subscriptions/?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  putGS('Subscriptions'); ?></A>
		<?php  } else { ?>
			&nbsp;
		<?php  }
    } else { ?>
			<A HREF="/admin/users/access.php?User=<?php  pgetUVar($Users,'Id'); ?>"><?php  putGS('Rights'); ?></A>
<?php  } ?>		</TD>
	<?php  } else { ?>
		<TD ALIGN="CENTER">
			<?php  if (getVar($Users,'Reader') == "Y") putGS('Yes'); else putGS('No'); ?>
                </TD>  
	<?php  }
	
	if ($dua != 0) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/admin/users/del.php?User=<?php  pgetVar($Users,'Id'); ?>"><IMG SRC="/admin/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete user $1',getHVar($Users,'Name')); ?>" TITLE="<?php  putGS('Delete user $1',getHVar($Users,'Name')); ?>"></A>
		</TD>
	<?php  } ?>
	</TR>
<?php 
    $i--;
    }
    }
?>	<TR><TD COLSPAN="2" NOWRAP>
<?php  if ($UserOffs <= 0) { ?>		&lt;&lt; <?php  putGS('Previous'); ?>
<?php  } else { ?>		<B><A HREF="index.php?sUname=<?php  pencURL($sUname); ?>&sType=<?php  pencURL($sType); ?>&UserOffs=<?php  p($UserOffs - $lpp); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
<?php  }

    if ($nr < $lpp+1) { ?>		 | <?php  putGS('Next'); ?> &gt;&gt;
<?php  } else { ?>		 | <B><A HREF="index.php?sUname=<?php  pencURL($sUname); ?>&sType=<?php  pencURL($sType); ?>&UserOffs=<?php  p($UserOffs + $lpp); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
<?php  } ?>	</TD></TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>
<?php CampsiteInterface::CopyrightNotice(); ?>
</BODY>
<?php  } ?>

</HTML>
